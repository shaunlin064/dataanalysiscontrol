<?php

namespace App;

use App\Http\Controllers\FinancialController;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FinancialList extends Model
{
    //

	protected $guarded = ['med_group','dep_name','dep_id','username','priority'];

	protected $attributes = [
	 'status' => 0,
        'profit_percentage' => 0,
        'companies_id' => 0,
	];

	protected $keyReplace = [
	  'memberid' => 'erp_user_id',
		'currency_id' => 'currency',
		'year_month' => 'set_date',
		'cam_id' => 'campaign_id',
		'o_id' => 'cp_detail_id'
	];

	public function __construct ()
	{
		parent::__construct();
	}

	public function bonus()
	{
		return $this->hasOne(Bonus::CLASS, 'erp_user_id','erp_user_id')->where('set_date',$this->set_date);
	}

	public function provide()
	{
		return $this->hasOne(Provide::CLASS, 'financial_lists_id','id');
	}

	public function receipt()
	{
		return $this->hasOne(FinancialReceipt::CLASS, 'financial_lists_id','id');
	}

	public function user(){
		return $this->hasOne(User::CLASS,'erp_user_id','erp_user_id');
	}

	public function saleGroups(){

		return $this->hasOne(SaleGroupsUsers::CLASS, 'erp_user_id','erp_user_id')->where('set_date',$this->set_date);
	}

	public function dataFormat ()
	{
		$this->keyChange()->valueChange();
		return $this;
	}

	public function rawKeyChange ($data)
	{
		foreach ($data as $index => $attribute) {

			if(in_array($index,array_keys($this->keyReplace))){

				$key = $this->keyReplace[$index];
				$data[$key] = $attribute;
				unset($data[$index]);
			}
		}
		return $data;
	}

	public function keyChange ()
	{
		$attributes = $this->getAttributes();

		$this->setRawAttributes($this->rawKeyChange($attributes));

		return $this;
	}

	public function revertKeyChange (){

		$attributes = $this->getAttributes();

		foreach ($attributes as $index => $attribute) {

			if(in_array($index,$this->keyReplace)){

				$key = array_flip($this->keyReplace)[$index];
				$attributes[$key] = $attribute;
				unset($attributes[$index]);

			}
		};
		$this->setRawAttributes($attributes);

		return $this;
	}

	public function valueChange ()
	{
		$this->set_date = date('Y-m-d',strtotime($this->set_date.'01'));
		if($this->profit_percentage == null){
			$this->profit_percentage = 0;
		}
		return $this;
	}

	public function revertValueChange ()
	{
		$this->year_month = date('Ym',strtotime($this->year_month));
		if($this->profit_percentage == null){
			$this->profit_percentage = 0;
		}
		return $this;
	}

	public function updateSet ($id,$data)
	{
		$data = $this->rawKeyChange($data);

		if($data['profit_percentage'] == null){
			$data['profit_percentage'] = 0;
		}
		//guarded
		foreach ($this->guarded as $unsetField){
			unset($data[$unsetField]);
		}
		// ex guarded
		unset($data['set_date']);
		unset($data['cp_detail_id']);
		unset($data['campaign_id']);

		if($this->checkDiff($id, $data)){
			return $this->where('id', $id)->update($data);
		}
	}

	/**
	 * @param $v
	 * @return mixed
	 */
	function setStatusTime ($v)
	{
		$v['paymentStatus'] = isset($v['receipt']['created_at']) ? substr($v['receipt']['created_at'], 0, 10) : 'no';
		$v['bonusStatus'] = isset($v['provide']['updated_at']) ? substr($v['provide']['updated_at'], 0, 10) : 'no';
		return $v;
	}

	/**
	 * @param $id
	 * @param string $dateStart
	 * @param string $dateEnd
	 * @return bool|mixed|string
	 */
	public function getFinancialData (Array $erpUserIds, string $dateStart,string $dateEnd = null)
	{
		$dateStart = new \DateTime($dateStart);
		$dateEnd = $dateEnd ? new \DateTime($dateEnd) :  $dateStart;

//        $datetime = new DateTime(date('Ym01'));

//        if($dateStart->format('Ym01') >= $datetime->format('Ym01') || $dateEnd->format('Ym01') >= $datetime->format('Ym01') ) {
//            $this->saveCloseData($dateStart->format('Ym01'));
//        }else{
//            $nowdate = new DateTime();
//            if($nowdate->format('d') < 16){
//                $datetime->modify('-1Month');
//                $this->saveCloseData($datetime->format('Ym01'));
//            }
//        }

		$erpReturnData = $this->whereIn('erp_user_id' , $erpUserIds)->whereBetween('set_date' ,[$dateStart->format('Y-m-01'),$dateEnd->format('Y-m-01')])->with('receipt')->with('provide')->get()
			->map(function ($v){

				$v = $this->setStatusTime($v);
				$v->sale_group_name = $v->saleGroups->saleGroups->name ?? '';
				$v->user_name =  ucfirst($v->user->name);
				$v->sale_group_id = $v->saleGroups->sale_groups_id ?? 0;
				return $this->exchangeMoney($v);
				//return $this->exchangeMoney($v->revertKeyChange()->revertValueChange());
			})
		 ->where('profit','!=',0)->values()->toArray();

		return $erpReturnData;
	}

	//存入所有資料 資料重抓使用 （慎）
	public function saveUntilNowAllData ()
	{
		$financial = new FinancialController();
		$erpReturnData = collect($financial->getErpMemberFinancial(['all'],'all'));

		DB::beginTransaction();
		try{
			$newdata = [];
			foreach ($erpReturnData as $erpReturnDatum) {
				$financeList = new FinancialList();
				$financeList->fill($erpReturnDatum)->dataFormat()->save();
			}

			DB::commit();

		} catch (\Exception $ex) {
			DB::rollback();
			dd($ex->getMessage());
		}
	}

	//存入現在已完成結帳月份的資料
	public function saveCloseData ( $yearMonthDayStr = null )
	{
		$financial = new FinancialController();

		if(empty($yearMonthDayStr)){
            $date = new DateTime(date('Ym01'));
        }else{
            $date = new DateTime($yearMonthDayStr);
        }

		$erpReturnData = collect($financial->getErpMemberFinancial(['all'],$date->format('Ym')));

        $erpReturnData = $erpReturnData->filter(function($v,$k) {
            $tmpDate = new DateTime($v['year_month'].'01');
            $setDate = $tmpDate->format('Y-m-d');
            $searchData = $this->where(['cp_detail_id'=>$v['o_id'] , 'set_date' => $setDate]);
            if($searchData->exists()){
                $this->updateSet($searchData->first()['id'],$v);
            }else{
                return $v;
            }
        });

        /*刪除作廢案件資料*/
        $erpReturnVoidData = collect($financial->getErpMemberCancelFinancial(['all'],$date->format('Ym')))->pluck('o_id')->toArray();


//		$alreadySetData = $this->where('set_date','>=',$date->format('Y-m-d'))->get();
//
//		$updateDataTmp = [];
//		// 先過濾已新增過的資料 做 UPDATA 為新增的資料做 insert
//		$erpReturnData = $erpReturnData->filter(function($v,$k) use($alreadySetData,&$updateDataTmp){
//
//			$tmpDate = new DateTime($v['year_month'].'01');
//			$setDate = $tmpDate->format('Y-m-d');
//			$data = $alreadySetData->where('cp_detail_id',$v['o_id'])->where('set_date',$setDate)->first();
//
//			if(empty($data)){
//				return $v;
//			}else if(!empty($data)){
//				//debug check
//				$updateDataTmp[]= $v;
//				$this->updateSet($data['id'],$v);
//			}
//		});
		DB::beginTransaction();
		try{
			$erpReturnData->each(function($v){
				$financeList = new FinancialList();
				$financeList->fill($v)->dataFormat()->save();
			});
            $this->whereIn('cp_detail_id',$erpReturnVoidData)->delete();
			DB::commit();


		} catch (\Exception $ex) {
			DB::rollback();
			\Log::error($ex->getMessage());
		}
	}

	public function exchangeMoney ($items)
	{
		$set_date = $items->set_date ??  date('Y-m-01',strtotime($items['year_month'].'01'));
		$currency = $items->currency ?? $items['currency_id'];
		$organization = $items->organization ?? $items['organization'];
		$exchangeRate = ExchangeRate::where(['set_date'=> $set_date,'currency'=>$currency])->first();

		//台灣傑思後台 只能填 台幣
		if( $organization == 'js' ){
			return $items;
		};
		switch($currency){
			case 'USD':
				if(empty($exchangeRate)){
					$exchangeRate = 31;
					//$exchangeRate = 1;
				}else{
					$exchangeRate = $exchangeRate->rate;
				}
				break;
			case 'JPY':
				if(empty($exchangeRate)){
					$exchangeRate = 0.2875;
					//$exchangeRate = 1;
				}else{
					$exchangeRate = $exchangeRate->rate;
				}
				break;
			default:
				$exchangeRate = 1;
				break;
		}
		if( in_array($currency,['USD','JPY']) ){
			$tmpData = [
			 'income' => round(($items->income ?? $items['income']) * $exchangeRate),
			 'cost' => round(($items->cost ?? $items['cost']) * $exchangeRate),
			];
			$tmpData['profit'] = $tmpData['income'] - $tmpData['cost'];

			//TODO 毛利小於零 是否需要扣1%
			if($items->organization ?? $items['organization'] == 'hk'){
				$tmpData['profit']= round($tmpData['profit'] * 0.99);
			}
			switch(gettype($items)){
				case 'object':
					foreach ($tmpData as $key => $item){
						$items->$key = $item;
					}
					break;
				default:
					foreach ($tmpData as $key => $item){
						$items[$key]= $item;
					}
			}
		}
		return $items;

	}

	public function getUserLatelyProfit ($erpUserId)
	{
		$thisMonth = new \DateTime();
		$thisMonth = $thisMonth->format('Y-m-01');

		$lastMonth = new \DateTime('last day of last month');
		$lastMonth = $lastMonth->format('Y-m-01');

        $allProfitBySetDate = $this->where('erp_user_id' ,$erpUserId)
                                ->where('profit','<>',0)->get()
                                ->map(function($v){ return $this->exchangeMoney($v); })
                                ->groupBy('set_date')->map(function($item){ return $item->sum('profit'); });

		$thisMonthProfit = $allProfitBySetDate->get($thisMonth) ?? 0;
		$lastMonthProfit = $allProfitBySetDate->get($lastMonth) ?? 0;
		$highestProfit = empty($allProfitBySetDate) ? 0 : $allProfitBySetDate->max();

		return [$highestProfit,$thisMonthProfit, $lastMonthProfit];
	}

	/**
	 * @param $id
	 * @param $data
	 * @return mixed
	 */
	private function checkDiff ($id, $data)
	{
		$orginal = collect($this->where('id', $id)->first()->toArray());
		$orginal->forget('set_date');
		$orginal->forget('cp_detail_id');
		$orginal->forget('campaign_id');
		$orginal->forget('id');
		$orginal->forget('status');
		$orginal->forget('created_at');
		$orginal->forget('updated_at');
		return count($orginal->diffAssoc($data)) > 0 ? true : false;
	}

    public function getDataList ($fieldName,$fieldid)
    {
        $data = $this->all()->pluck($fieldName,$fieldid)->filter()->unique()->map(function($v,$id){
            $tmp = ['name'=>$v,'id'=>$id];
            return $tmp;
        })->sortBy('name')->values()->toArray();

        return $data;
	}
}

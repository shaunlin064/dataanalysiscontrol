<?php

namespace App;
ini_set('max_execution_time', 600);
ini_set('memory_limit','1024M');
use App\Http\Controllers\FinancialController;
use Illuminate\Database\Eloquent\Model;

class FinancialReceipt extends Model
{
    //
	protected $fillable = ['financial_lists_id','created_at','updated_at'];
	
	public function updateFinancialMoneyReceipt ($type='select')
	{
		$financial = new FinancialController();
		/*cp_detail_id and balance date */
        collect($financial->getBalancePayMentData($type))->each(function($balanceData){
           
            $results = FinancialList::where('cp_detail_id',$balanceData['cp_detail_id'])->get();
            $financialListIdReceipMoney = $results->filter(function($item,$k) use($balanceData){
                /*一對多的情況 需要判斷 financial set_date 是小於 收款日 才更新*/
                return $item['set_date'] < date("Y-m-d",strtotime($balanceData['balance_date']));
            })->pluck('id');
            /*更新financialList狀態*/
            
            FinancialList::whereIn('id',$financialListIdReceipMoney)->where('status',0)->update(['status' => 1]);
            /*建立 financialReceipt 未存在才建立*/
            $financialListIdReceipMoney->reject(function($v){
                return $this->where('financial_lists_id',$v)->exists();
            })->map(function ($v) use($balanceData){
                $this->create(['financial_lists_id' => $v,'created_at' => $balanceData['balance_date']]);
            });
            
        });
	}
	public function checkinPassData( FinancialList $v)
	{
		$nowAvalibelUser = ['ids' => [67, 84, 131, 132, 133, 136, 153, 170, 174, 181, 186, 188,200,201,204,205],
		 'setDate' => config('custom.setOldDateLine')];
		$leaveUser1 = ['ids' => [97, 175],
		 'setDate' => '2019-03-01']; //3月以前不用
		$leaveUser2 = ['ids' => [156, 161],
		 'setDate' => '2019-04-01']; //4月以前不用
        $leaveUser3 = ['ids' => [122],
            'setDate' => '2019-07-01']; //7月以前不用
        $leaveUser4 = ['ids' => [155],
            'setDate' => '2019-08-01']; //8月以前不用
		if (in_array($v->erp_user_id, $leaveUser1['ids']) && $v->set_date < $leaveUser1['setDate']) {
			$this->providOldData($v);
		} elseif (in_array($v->erp_user_id, $leaveUser2['ids']) && $v->set_date < $leaveUser2['setDate']) {
			$this->providOldData($v);
		}elseif (in_array($v->erp_user_id, $leaveUser3['ids']) && $v->set_date < $leaveUser3['setDate']) {
            $this->providOldData($v);
        }elseif (in_array($v->erp_user_id, $leaveUser4['ids']) && $v->set_date < $leaveUser4['setDate']) {
            $this->providOldData($v);
        }elseif(in_array($v->erp_user_id,$nowAvalibelUser['ids']) && $v->set_date < $nowAvalibelUser['setDate']){
			$this->providOldData($v);
		}
	}
	public function providOldData(FinancialList $finListObj){
		//save financialList
		$finListObj->status = 2;
		$finListObj->save();
		
		//calculat exchangeProfit
		$exchangeProfitMoney = $finListObj->exchangeMoney($finListObj)->profit;
		
		$oldCreated_at = new \DateTime($finListObj->set_date);
		$oldCreated_at->modify('+4 month');
		
		$financial_lists_id = $finListObj->id;
		$bonusReach = isset($finListObj->bonus) ? $finListObj->bonus->bonusReach : [];
		
		$bonusId = $bonusReach->bonus_id ?? 0;

		$reachRate = $bonusReach->reach_rate ?? 0;
		
		$provideMoney = $exchangeProfitMoney * $reachRate / 100;
		
		$provide = Provide::where('financial_lists_id', $financial_lists_id)->first();
		
		$provideData = [
		 'bonus_id' => $bonusId,
		 'financial_lists_id' => $financial_lists_id,
		 'provide_money' => $provideMoney
		];
		
		if (isset($provide)) {
			//update
			foreach ($provideData as $key => $item) {
				$provide->$key = $item;
			}
			$provide->save();
		} else {
			//new
			$provideData['created_at'] = $oldCreated_at->format('Y-m-01 H:i:s');
			$provideData['updated_at'] = $oldCreated_at->format('Y-m-01 H:i:s');
			$oldCreated_at->modify('-1 month');
			
			if(isset($finListObj->receipt)){
                $finListObj->receipt->update(['created_at'=> $oldCreated_at->format('Y-m-01 H:i:s'),'updated_at'=> $oldCreated_at->format('Y-m-01 H:i:s')]);
            }
			
			Provide::create($provideData);
		}
	}
}

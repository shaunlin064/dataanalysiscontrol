<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-07-31
	 * Time: 11:06
	 */
	
	namespace App\Http\Controllers\Financial;
	
	
	use App\FinancialList;
	use App\FinancialReceipt;
	use App\Http\Controllers\BaseController;
	use App\Http\Controllers\FinancialController;
	use DateTime;
	use DB;
	
	class FinancialListController extends BaseController
	{
		protected $acceptDate;
		
		public function __construct ()
		{
			parent::__construct();
			
			//抓取資料條件日期 判斷現在日期是否 超過 15號結帳日 已過結帳日 則代表上個月已完成結帳
			$date = new DateTime(date('Ym01'));
			if( date('d') > 15 ){
				$this->acceptDate = $date->modify('-1Month');
			}else{
				$this->acceptDate = $date->modify('-2Month');
			};
		}
		
		public function updateFinancialMoneyReceipt ($type='select')
		{
			$financial = new FinancialController();
			
			$erpReturnData = collect($financial->getBalancePayMentIds($type));
			$v = FinancialList::whereIn('cp_detail_id',$erpReturnData)->get();
			FinancialList::whereIn('cp_detail_id',$erpReturnData)->update(['status' => 1]);
			
			$v->map(function ($v){
				if(!FinancialReceipt::where('financial_lists_id',$v->id)->exists()){
					FinancialReceipt::create(['financial_lists_id' => $v->id]);
				}
			});
		}
		//存入所有資料 資料重抓使用 （慎）
		public function saveUntilNowAllData ()
		{
			$financial = new FinancialController();
			$erpReturnData = collect($financial->getErpMemberFinancial(['all'],'all'));
			$erpReturnData = $erpReturnData->where('year_month' ,'<=' ,$this->acceptDate->format('Ym'))->toArray();
			
			DB::beginTransaction();
			try{
				
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
		public function saveCloseData ()
		{
			$financial = new FinancialController();
			
			$erpReturnData = collect($financial->getErpMemberFinancial(['all'],'all'));
			$alreadySetData = collect(FinancialList::where('set_date','>=',$this->acceptDate->format('Y-m-d'))->get()->toArray());
			$financeList = new FinancialList();
			$updateDataTmp = [];
			//dd($erpReturnData->where('memberid',181)->where('year_month','201908'));
			// 先過濾已新增過的資料 做 UPDATA 為新增的資料做 insert
			$erpReturnData = $erpReturnData->filter(function($v,$k) use($financeList,$alreadySetData,&$updateDataTmp){
				
				$data = $alreadySetData->where('cp_detail_id',$v['o_id'])->where('set_date',date("Y-m-01",strtotime($v['year_month'].'01')))->first();
		
				if(empty($data) && $financeList->where(['cp_detail_id' => $v['o_id'], 'set_date' => date("Y-m-01",strtotime($v['year_month'].'01')) ])->doesntExist()){
					return $v;
				}else if(!empty($data)){
					//debug check
					$updateDataTmp[]= $v;
					$financeList->updateSet($data['id'],$v);
				}
			});
			
			DB::beginTransaction();
			
			try{
				$erpReturnData->each(function($v){
					$financeList = new FinancialList();
					$financeList->fill($v)->dataFormat()->save();
				});
				DB::commit();
				
			} catch (\Exception $ex) {
				DB::rollback();
				\Log::error($ex->getMessage());
			}
		}

	}

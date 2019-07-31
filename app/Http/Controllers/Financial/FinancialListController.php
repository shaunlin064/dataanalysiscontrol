<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-07-31
	 * Time: 11:06
	 */
	
	namespace App\Http\Controllers\Financial;
	
	
	use App\FinancialList;
	use App\Http\Controllers\BaseController;
	use App\Http\Controllers\FinancialController;
	use DateTime;
	
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
		
		//存入所有資料 資料重抓使用 （慎）
		public function saveUntilNowAllData ()
		{
			$financial = new FinancialController();
			$erpReturnData = collect($financial->getErpMemberFinancial(['all'],'all'));
			$erpReturnData = $erpReturnData->where('year_month' ,'<=' ,$this->acceptDate->format('Ym'))->toArray();
			foreach ($erpReturnData as $erpReturnDatum) {
				$financeList = new FinancialList();
				$financeList->fill($erpReturnDatum)->dataFormat()->save();
			}
		}
		
		//存入現在已完成結帳月份的資料
		public function saveCloseData ()
		{
			$financial = new FinancialController();
			$erpReturnData = collect($financial->getErpMemberFinancial(['all'],$this->acceptDate->format('Ym')));
			$alreadySetData = collect(FinancialList::where('set_date',$this->acceptDate->format('Y-m-d'))->get()->toArray());
			
			$erpReturnData = $erpReturnData->whereNotIn('cp_detail_id', $alreadySetData->pluck('cp_detail_id'));
			
			$erpReturnData->each(function($v){
				$financeList = new FinancialList();
				$financeList->fill($v)->dataFormat()->save();
			});
			
		}
	}

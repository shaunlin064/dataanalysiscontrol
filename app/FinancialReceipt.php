<?php

namespace App;

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
		$erpReturnData = collect($financial->getBalancePayMentData($type));
		$cpDetailIds = $erpReturnData->pluck('cp_detail_id');
		
		$v = FinancialList::whereIn('cp_detail_id',$cpDetailIds)->get();
		FinancialList::whereIn('cp_detail_id',$cpDetailIds)->update(['status' => 1]);
		
		$v->map(function ($v) use($erpReturnData){
			if(!$this->where('financial_lists_id',$v->id)->exists()){
				$this->create(['financial_lists_id' => $v->id,'created_at' => $erpReturnData->where('cp_detail_id',$v->cp_detail_id)->first()['balance_date']]);
			}
			
			// 過往資料直接過濾至已發款
			if($v->set_date < '2019-06-01'){
				$this->checkinPassData($v);
			}
			
		});
		
	}
	public function checkinPassData($v)
	{
		$nowAvalibelUser = ['ids' => [67, 84, 122, 131, 132, 133, 136, 153, 155, 170, 174, 181, 186, 188],
		 'setDate' => '2019-06-01'];//5月以前不用
		$leaveUser1 = ['ids' => [97, 175],
		 'setDate' => '2019-03-01']; //3月以前不用
		$leaveUser2 = ['ids' => [156, 161],
		 'setDate' => '2019-04-01']; //4月以前不用
		
		if (in_array($v->erp_user_id, $leaveUser1['ids']) && $v->set_date < $leaveUser1['setDate']) {
			$this->providOldData($v);
		} elseif (in_array($v->erp_user_id, $leaveUser2['ids']) && $v->set_date < $leaveUser2['setDate']) {
			$this->providOldData($v);
		}else{
			$this->providOldData($v);
		}
	}
	public function providOldData($finListObj){
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
		 'provide_money' => $provideMoney > 0 ? $provideMoney  : 0
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
			$finListObj->receipt->update(['created_at'=> $oldCreated_at->format('Y-m-01 H:i:s'),'updated_at'=> $oldCreated_at->format('Y-m-01 H:i:s')]);
			Provide::create($provideData);
		}
	}
}

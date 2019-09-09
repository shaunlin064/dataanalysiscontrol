<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleGroupsReach extends Model
{
    //
	protected $attributes = [
	 'status' => 0,
	];
	
	protected $fillable = ['status','sale_groups_id','sale_groups_bonus_levels_id','sale_groups_users_id','set_date','groups_profit','provide_money'];
	
	

	public function setConvenerReach ($setDate,$saleGroupsId,$saleGroupsUsersId)
	{
		$sameGroupErpUserIds = SaleGroupsUsers::where(['sale_groups_id' => $saleGroupsId,'set_date' => $setDate])->get()->pluck('erp_user_id');
		//找出所有財報資料 外匯轉換
		$objFinaniial = FinancialList::whereIn('erp_user_id',$sameGroupErpUserIds)->where('set_date',$setDate)->get();
		//取總毛利
		$groupsTotalProfit = $objFinaniial->map(function($v,$k){
			return $v->exchangeMoney($v);
		})->sum('profit');
		
		//計算觸及之獎金階級 與 獎金
		$saleGroupsBonus = new SaleGroupsBonusLevels();
		$finalBonus = $saleGroupsBonus->getReachBonusLevels($groupsTotalProfit,$saleGroupsId,$setDate);

		/*如未設定招集人此月份資料則會出錯*/
		$provideMoney =  isset($finalBonus->bonus_rate) ? round($groupsTotalProfit * ( $finalBonus->bonus_rate / 100 )) : 0 ;
		
		$newData = SaleGroupsReach::create([
		 'sale_groups_id' => $saleGroupsId,
		 'sale_groups_users_id' => $saleGroupsUsersId,
		 'sale_groups_bonus_levels_id' => $finalBonus->id ?? 0,
		 'set_date' => $setDate,
		 'groups_profit' => $groupsTotalProfit,
		 'provide_money' => $provideMoney
		]);
		
		return $newData ?? [];
	}
	
	public function cleanData ($saleGroupsId,$saleGroupsUsersId,$setDate)
	{
		$reachObj = $this->where(['sale_groups_id' => $saleGroupsId,'sale_groups_users_id' => $saleGroupsUsersId,'set_date' =>$setDate]);
		if($reachObj->exists()){
			$reachObj->delete();
		}
	}
	
	public function setAllConvenerReach ($setDate)
	{
		$groupsConveners = SaleGroupsUsers::where(['is_convener'=>1,'set_date'=>$setDate])->get();
		// loop 招集人資料計算 此樂團擊達成率、獎金、毛利
		foreach($groupsConveners as $groupsConvener){
			//計算此組別此月毛利
			//找與招集人同一團的人員id
			$saleGroupsId = $groupsConvener->sale_groups_id;
			$saleGroupsUsersId = $groupsConvener->id;
			$this->cleanData($saleGroupsId,$saleGroupsUsersId,$setDate);
			$newData[] = $this->setConvenerReach($setDate,$saleGroupsId,$saleGroupsUsersId);
		}
		return $newData ?? [] ;
	}
}

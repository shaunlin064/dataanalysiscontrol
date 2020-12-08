<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleGroupsReach extends Model
{
    //
	protected $attributes = [
	 'status' => 0,
	];
	
	protected $fillable = ['status','sale_groups_id','sale_groups_bonus_levels_id','sale_groups_users_id','set_date','groups_profit','provide_money','rate','updated_at'];

	public function saleGroups(){
		return $this->belongsTo(SaleGroups::Class,'sale_groups_id');
	}
	public function saleUser(){
		return $this->belongsTo(SaleGroupsUsers::Class,'sale_groups_users_id');
	}
	public function setConvenerReach ($setDate,$saleGroupsId,$saleGroupsUsersId)
	{
		$sameGroupErpUserIds = SaleGroupsUsers::where(['sale_groups_id' => $saleGroupsId,'set_date' => $setDate])->get();
		
		$sameGroupErpUserIds = $sameGroupErpUserIds->pluck('erp_user_id');
		
		/*TODO:: 確認團隊名單內 責任額為0是否還需要計算業績 以相同邏輯情況下 責任額歸責任額 業績還是要計算, 如不計算就移出團隊即可*/
		//找出所有財報資料 外匯轉換
		$objFinaniial = FinancialList::whereIn('erp_user_id',$sameGroupErpUserIds)->where('set_date',$setDate)->get();
		
		//取總毛利
		$groupsTotalProfit = round($objFinaniial->map(function($v,$k){
			return $v->exchangeMoney($v);
		})->sum('profit'));
		
		//計算觸及之獎金階級 與 獎金
		$saleGroupsBonus = new SaleGroupsBonusLevels();
		
		/*TODO::計算英雄榜獎金 與 呈現 英雄榜獎金沒有透過系統發放 而是公司大會人工發放 故沒有計算進系統內*/
		$finalBonus = $saleGroupsBonus->getReachBonusLevels($groupsTotalProfit,$saleGroupsId,$setDate);
		$rate = SaleGroupsRate::where(['sale_groups_id' => $saleGroupsId , 'set_date' => $setDate])->first()['rate'] ?? 0;
		$provideMoney = ( $rate > 0  && $groupsTotalProfit > 0 ) ? round($groupsTotalProfit * ($rate / 100)) : 0;
		
		$newData = SaleGroupsReach::create([
		 'sale_groups_id' => $saleGroupsId,
		 'sale_groups_users_id' => $saleGroupsUsersId,
		 'sale_groups_bonus_levels_id' => $finalBonus->id ?? 0,
		 'set_date' => $setDate,
		 'groups_profit' => $groupsTotalProfit,
		 'rate' => $rate,
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
		// loop 招集人資料計算 此團績達成率、獎金、毛利
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
	
	static function getBonusMembersBeyondMoney($setDate,$erpUserId){
		$percentage = BonusReach::getUserProfitPercentage($setDate, $erpUserId);
		$result = [
			'percentage' => $percentage,
			'bonus_direct' => 0,
			'reach_level_rate' => 0,
		];
		foreach(config('sale_group.bonusMembersBeyondLevel.level') as $level){
			if($percentage > $level['rate']){
				$result['bonus_direct'] = $level['bonus_direct'];
				$result['reach_level_rate'] = $level['rate'];
			}
		}
		return $result;
	}
	
	static function getProfit( $saleGroupId, $setDate ){
		$saleGroupErpUserId = SaleGroups::find($saleGroupId)->groupsUsers->where('set_date', $setDate)->pluck('erp_user_id');
		return round(FinancialList::whereIn('erp_user_id', $saleGroupErpUserId)
		                    ->where('set_date', $setDate)
		                    ->sum('profit'));
	}
	
	static function getBonusDirect($saleGroupId,$setDate,$percentage){
		$bonus_direct = 0;
		foreach ( SaleGroups::find($saleGroupId)->groupsBonus->where('set_date',$setDate)->sortBy('achieving_rate')->values() as $key => $items )
		{
			if (  $percentage > $items['achieving_rate']  )
			{
				$bonus_direct = $items['bonus_direct'];
			}
		}
		
		return $bonus_direct;
	}
}

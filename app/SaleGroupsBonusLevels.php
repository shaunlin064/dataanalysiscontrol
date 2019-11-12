<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleGroupsBonusLevels extends Model
{
    //
	protected $fillable = ['set_date','achieving_rate','bonus_rate','bonus_direct'];
	
	public function getBonusLevels ($userGroupsId,$setDate)
	{
		//取設定月份 bonus level 招集人獎金表
		
		return $this->where(['sale_groups_id'=>$userGroupsId,'set_date'=>$setDate])->orderBy('achieving_rate','asc')->get();
	}
	
	public function getReachBonusLevels($totalProfit,$userGroupsId,$setDate){
		//取設定月份 bonus level 招集人獎金表
		$bonusLevels = $this->getBonusLevels($userGroupsId,$setDate);
		
		//取到達 bonus
		$maxAchievingMoney = $bonusLevels->filter(function($v,$k) use($totalProfit){
			return $totalProfit > $v->achieving_rate;
		})->max('achieving_rate');
		$finalBonus = $bonusLevels->firstwhere('achieving_rate',$maxAchievingMoney);
		
		//回傳最後達到的 bonus
		return $finalBonus;
	}
	
	public function getCalculationcalProfit ($totalProfit,$userGroupsId,$setDate)
	{
		$finalBonus = $this->getReachBonusLevels($totalProfit,$userGroupsId,$setDate);
		return round($totalProfit * ($finalBonus->bonus_rate / 100));
	}
}

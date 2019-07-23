<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\BonusLevels;

class Bonus extends Model
{
    //
	protected $table = 'bonus';
	protected $fillable = ['user_id','set_date','boundary'];
	
	public function levels()
	{
		return $this->hasMany(BonusLevels::CLASS);
	}
	
	public function getUserBonus ($uid, $totalProfit,String $dateYearMonth)
	{
		//待解 如 搜尋舊資料 但當時未設定 bonus 預設要抓最新 or 當時前後？
		//目前預設抓最新設定
		
		//check exists or use Old Data
		if($this->where(['user_id' => $uid,'set_date' => $dateYearMonth])->exists()){
			
			$userbonus = $this->where([
			 'user_id' => $uid,
			 'set_date' => $dateYearMonth
			])->with('levels')->get()->first()->toArray();
			
		}else{
			//抓取 最新一筆設定資料
//			if( $this->where(['user_id' => $uid,])->with('levels')->exists() ){
//				$userbonus = $this->where(['user_id' => $uid,])->with('levels')->OrderBy('id','desc')->get()->first()->toArray();
//
//			}else{
				$userbonus = [
				 'boundary' => 0,
				'levels' => [
					 [
					  'achieving_rate' => 0,
					  'bonus_rate' => 0
						]
					]
				];
//			}
		}
		$reachLevle = null;
		$nextLevel = null;
		
		foreach($userbonus['levels'] as $key => $items){
			$thisLevelAchieving = $userbonus['boundary'] * $items['achieving_rate'] * 0.01;
			if( $totalProfit >  $thisLevelAchieving){
				$reachLevle = $items;
			}else{
				$nextLevel = $items;
				break;
			}
		}
		
		//if not reach minleavel
		if($reachLevle == null){
			$estimateBonus = 0;
			if($totalProfit == 0 && $userbonus['boundary'] == 0 && $nextLevel['achieving_rate'] == 0){
			
			}else if($userbonus['boundary'] == 0 && $nextLevel['achieving_rate'] == 0){
			
			}else{
				$nextLevel['bonus_next_amount'] = $userbonus['boundary'] * $nextLevel['achieving_rate'] * 0.01 - $totalProfit;
//				$nextLevel['bonus_next_percentage'] = round($totalProfit/ ($userbonus['boundary'] * $nextLevel['achieving_rate'] * 0.01)*100);
				$nextLevel['bonus_next_percentage'] = round($totalProfit / $userbonus['boundary'] * 100 );
			}
		}else{
			
			$reachLevle['bonus_direct'] = $reachLevle['bonus_direct'] ?? 0;
			$estimateBonus =  round($totalProfit * $reachLevle['bonus_rate'] * 0.01) + $reachLevle['bonus_direct'];
			if($nextLevel){
				$nextLevel['bonus_next_amount'] = $userbonus['boundary'] * $nextLevel['achieving_rate'] * 0.01 - $totalProfit;
//				$nextLevel['bonus_next_percentage'] = round($totalProfit/ ($userbonus['boundary'] * $nextLevel['achieving_rate'] * 0.01)*100);
			}
			
			$nextLevel['bonus_next_percentage'] = $userbonus['boundary'] != 0 ?round($totalProfit / $userbonus['boundary'] * 100 ) : 0;
		}
		
		$bonusDirect = isset($reachLevle['bonus_direct']) ? $reachLevle['bonus_direct'] : 0;
		
		
		$returnData = [ 'estimateBonus'=>$estimateBonus,'reachLevle' => $reachLevle , 'nextLevel' => $nextLevel,'bonusDirect' => $bonusDirect ];
		return $returnData;
	}
}

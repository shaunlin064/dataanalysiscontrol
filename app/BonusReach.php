<?php

namespace App;

use App\Bonus;
use Illuminate\Database\Eloquent\Model;

class BonusReach extends Model
{
    //
	protected $table = 'bonus_reach';
	protected $fillable = ['bonus_id','reach_rate'];

	public function bonus()
	{
		return $this->belongsTo(Bonus::CLASS);
	}
	
	static function getUserProfitPercentage($setDate,$erpUserId){
		$profit = FinancialList::where(['set_date' => $setDate , 'erp_user_id' => $erpUserId])->sum('profit');
		$userBonus = \App\Bonus::where(['set_date' => $setDate , 'erp_user_id' => $erpUserId])->first();
		$percentage = 0;
		
		if(isset($userBonus['boundary'])){
			$percentage = $userBonus['boundary'] != 0 ? round($profit / $userBonus['boundary'] * 100) : 0;
		}
		return $percentage;
	}
}

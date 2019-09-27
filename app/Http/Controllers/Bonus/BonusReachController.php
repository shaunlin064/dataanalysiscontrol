<?php

namespace App\Http\Controllers\Bonus;

use App\Bonus;
use App\BonusReach;
use App\FinancialList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BonusReachController extends Controller
{
    //
	public function update ($startDate = '2018-01-01')
	{
		
		$users = Bonus::where('erp_user_id' ,'!=' , 0)->groupBy('erp_user_id')->get()->pluck('erp_user_id');
		
		$reviewController = new ReviewController();
		
		FinancialList::whereIn('erp_user_id' , $users)->get()->groupBy(['erp_user_id','set_date'])->map(function($datas,$erpUserId) use($reviewController,$startDate){
			
			$datas->map(function($financialLists,$setDate) use($erpUserId,$reviewController,$startDate){
				if($setDate < $startDate){
					return;
				}
				$bonus = Bonus::where(['erp_user_id' => $erpUserId,'set_date' => $setDate])->first();
				$totalProfit = $financialLists->sum('profit');
				// getUserBonus set output Data
				if(!empty($bonus) && $bonus->boundary != 0){
					$bonus_rate = $reviewController->getUserBonus($erpUserId, $totalProfit, $setDate)['bonus_rate'];
					BonusReach::create(['reach_rate' => $bonus_rate ?? 0 ,'bonus_id' => $bonus->id]);
				}
			});
		});;

	}
}

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
	public function update ($startDate = '2018-05-01')
	{
		$users = \App\User::all()->pluck('erp_user_id');
		
		$reviewController = new ReviewController();
		
		foreach ($users as $uId){
			$date_now =  new \DateTime();
			$date = new \DateTime($startDate);
			$bonus = Bonus::where(['erp_user_id' => $uId])->exists();
			if($bonus){
				while($date->format('Y-m-01') < $date_now->format('Y-m-01')){
					$financialList = FinancialList::where(['erp_user_id' => $uId, 'set_date' => $date->format('Y-m-01')])->exists();
					$bonus = Bonus::where(['erp_user_id' => $uId,'set_date' => $date->format('Y-m-01')])->select('id')->first();
					//get financialData
					if($financialList){
						$erpReturnData = $reviewController->getFinancialData($uId, $date->format('Y-m-01'), $date->format('Ym'));
						// loop calculation total Amount
						list($totalIncome, $totalCost, $totalProfit, $erpReturnData) = $reviewController->calculationTotal($erpReturnData);
						// getUserBonus set output Data
						$reachData = $reviewController->getUserBonus($uId, $totalProfit, $date->format('Y-m-01'));
						$boxData[$uId][$date->format('Y-m-01')] = $reachData;
						
						$bonusReach = BonusReach::create(['reach_rate' => $reachData['bonus_rate'],'bonus_id' => $bonus->id]);
						
					}
					$date->modify('+1Month');
				};
			}
		}
	}
}

<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SaleGroupsRateSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 * @throws \Exception
	 */
    public function run()
    {
    	$saleGroups = \App\SaleGroups::all();
    	
	    /*依據 原 SaleGroupsReach 內的 rate 重建sale group rate表*/
	    
	    $saleGroupsReaches = \App\SaleGroupsReach::select('sale_groups_id','rate','set_date')->get()->toArray();
	    \App\SaleGroupsRate::truncate();
	    \App\SaleGroupsRate::insert($saleGroupsReaches);
	    $dateRange = date_range('20180101', today()->modify("+1Month")->format('Ym01'));
	    
	    $saleGroups = \App\SaleGroups::all();
	    $saleGroups->each(function($v) use($dateRange){
		    foreach($dateRange  as $date){
			    if(\App\SaleGroupsRate::where(['sale_groups_id'=>$v->id,'set_date'=>$date])->doesntExist()){
			    	$lastSetDate = new Carbon($date);
				    $lastSetDate = $lastSetDate->modify('-1Month')->format('Y-m-01');
				    $lateRate = \App\SaleGroupsRate::where(['sale_groups_id'=>$v->id,'set_date'=>$lastSetDate])->first()['rate'] ?? 0;
				    $v->saleGroupsRate()->create(
					    [
						    'set_date' => $date,
						    'rate' => $lateRate,
					    ]
				    );
			    }
		    }
	    });
	   
	    
    }
}

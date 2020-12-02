<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SaleGroupsRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$saleGroups = \App\SaleGroups::all();
    	
	    /*依據 原 SaleGroupsReach 內的 rate 重建sale group rate表*/
	    $saleGroupsReaches = \App\SaleGroupsReach::select('sale_groups_id','rate','set_date')->get()->toArray();
	    \App\SaleGroupsRate::truncate();
	    \App\SaleGroupsRate::insert($saleGroupsReaches);
	    
	    $saleGroups = \App\SaleGroups::all();
	    
	    foreach( date_range('20180101', today()->format('Ym01')) as $date){
		    $saleGroups->each(function($v) use($date){
				if(! \App\SaleGroupsRate::where(['id'=>$v->id,'set_date'=>$date])->exists()){
					$v->saleGroupsRate()->create(
						[
							'set_date' => $date,
							'rate' => 0,
						]
					);
				}
		    });
	    }
	    
	    
	    
    }
}

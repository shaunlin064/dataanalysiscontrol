<?php

use Illuminate\Database\Seeder;

class SaleGroupsReachsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
	    $date = new DateTime(date('Ym01'));
	    while($date->format('Y-m-d') != '2018-01-01'){
			    $setDate = $date->modify('-1Month')->format('Y-m-d');
			    $saleGroupsReach = new \App\SaleGroupsReach();
			    $saleGroupsReach->setAllConvenerReach($setDate);
	    };
    }
}

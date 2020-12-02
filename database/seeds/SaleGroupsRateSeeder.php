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
	    /*依據 原 SaleGroupsReach 內的 rate 重建sale group rate表*/
	    $saleGroupsReaches = \App\SaleGroupsReach::select('sale_groups_id','rate','set_date')->get()->toArray();
	    \App\SaleGroupsRate::truncate();
	    \App\SaleGroupsRate::insert($saleGroupsReaches);
    }
}

<?php

namespace Database\Seeders;

use App\FinancialList;
use App\Http\Controllers\FinancialController;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
	    $financial = new FinancialController();
	
	    foreach (date_range('20180101', today()->format('Ym01')) as $date){
		    $newDate = new Carbon($date);
		
		    $erpReturnData = collect($financial->getErpMemberFinancial([ 'all' ], $newDate->format('Ym')))->whereIn('organization',
			    [ 'js', 'ff' ]);
		    $financialList = new FinancialList();
		    $financialList->customerSaveAndUpdate($erpReturnData->toArray());
		
	    }
	    
    }
}

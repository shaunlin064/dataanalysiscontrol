<?php

namespace App\Console\Commands;

use App\SaleGroupsReach;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReloadConvenerReach extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reload_convener_reach';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '重設所有招集人業績達標資料';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
	    $startDate = '2018-01-01';
	    
	    $date = new DateTime(date('Ym01'));
	    DB::table('sale_groups_reaches')->truncate();
	    $saleGroupsReach = new SaleGroupsReach();
	    
			while($date->format('Y-m-d') != $startDate){
				$setDate = $date->modify('-1Month')->format('Y-m-d');
				$data[] = $saleGroupsReach->setAllConvenerReach($setDate);
			}
	    collect($data)->flatten()->map(function($v,$k){
		    $date =  new DateTime($v->set_date);
		    $v->updated_at = $date->modify('+1Month')->format('Y-m-d H:i:s');
		    $v->status = 1;
		    $v->update();
	    });
	    
	    echo 'ReloadConvenerReach done';
    }
}

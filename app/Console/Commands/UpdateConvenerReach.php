<?php

namespace App\Console\Commands;

use App\SaleGroupsReach;
use DateTime;
use Illuminate\Console\Command;

class UpdateConvenerReach extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_convener_reach';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新設定上月所有達標招集人獎金資料 供獎金發放使用';

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
	    $date = new DateTime(date('Ym01'));
	    $setDate = $date->modify('-1Month')->format('Y-m-d');
	
	    $saleGroupsReach = new SaleGroupsReach();
	    $saleGroupsReach->setAllConvenerReach($setDate);
	    
			echo 'UpdateConvenerReach done';
    }
}

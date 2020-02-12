<?php

namespace App\Console\Commands;

use App\SaleGroups;
use App\SaleGroupsBonusLevels;
use App\SaleGroupsUsers;
use Illuminate\Console\Command;

class UpdateSaleGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_sale_groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '新增抓取上月資料 更新本月團隊資料 ';

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
        $startTime = microtime(true);
        
	    $thisMonth = date('Y-m-01');
	    $allSale = SaleGroups::where('id','!=',0)->with(['groupsUsersLastMonth'])->with(['groupsBonusLastMonth'])->get();
	    SaleGroupsBonusLevels::where('set_Date',$thisMonth)->delete();
	    SaleGroupsUsers::where('set_Date',$thisMonth)->delete();
	    foreach($allSale as $sale){
		    foreach (['groupsUsersLastMonth','groupsBonusLastMonth'] as $keyfiled){
			
			    if(isset($sale[$keyfiled])){
				    $tmp = [];
				
				    $sale[$keyfiled]->flatMap(function ($values,$key) use($thisMonth,&$tmp) {
					
					    $values = $values->toArray();
					    $unsetArr = ['id','created_at','updated_at'];
					
					    foreach($unsetArr as $filed){
						    unset($values[$filed]);
					    };
					    $values['set_date'] = $thisMonth;
					    $tmp[] =  $values;
				    });
				    $sale->$keyfiled()->createMany($tmp);
			    }
		    }
		    $sale->push();
	    };
	    
        $runTime = round(microtime(true) - $startTime, 2);
        echo ("Commands: {$this->signature} ({$runTime} seconds)\n");
    
        /*mail notice Job*/
        \App\Jobs\SentMail::dispatch('crontab',['name'=>'admin', 'title' => 'update_sale_groups schedule down']);
    }
}

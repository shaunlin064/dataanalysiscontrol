<?php

namespace App\Console\Commands;

use App\SaleGroups;
use App\SaleGroupsBonusLevels;
use App\SaleGroupsRate;
use App\SaleGroupsUsers;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
	    
	    SaleGroupsBonusLevels::where('set_date', $thisMonth)->delete();
	    SaleGroupsUsers::where('set_date', $thisMonth)->delete();
	    SaleGroupsRate::where('set_date', $thisMonth)->delete();
	    
	    $allSale = SaleGroups::where('id','!=',0)->with('groupsUsersLastMonth','groupsBonusLastMonth')->get();

	    try {
	    	
            DB::beginTransaction();
		    
		    
		    foreach($allSale as $sale){
			    $sale->refresh();
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
                
                /*set Rate like last month*/
			    $lastMonth = new Carbon(today()->modify('-1Month')->format('Y-m-01'));
			    $lastMonth = $lastMonth->format('Y-m-01');
			    $saleGroupsRate = $sale->saleGroupsRate->where('set_date',$lastMonth)->first()->toArray();
			    $saleGroupsRate['set_date'] = $thisMonth;
			    
			    $sale->saleGroupsRate()->create($saleGroupsRate);
			    
			    $sale->push();
            };
		    
            DB::commit();
	    } catch (\Exception $e) {
	        DB::rollBack();
            /*mail notice Job*/
            \App\Jobs\SentMail::dispatch('crontab',['mail'=>env('NOTIFICATION_EMAIL'),'name'=>'admin', 'title' =>"{$this->signature} error {$e->getMessage()}"]);
	    }

        $runTime = round(microtime(true) - $startTime, 2);
        echo ("Commands: {$this->signature} ({$runTime} seconds)\n");
    }
	
}

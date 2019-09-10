<?php

namespace App\Console\Commands;

use App\FinancialReceipt;
use App\FinancialList;
use App\Http\Controllers\FinancialController;
use App\Provide;
use Illuminate\Console\Command;

class SetOldProvide extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set_old_provide';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '設定過往資料為已收款狀態';

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
	    $financialList = FinancialList::where(['status' => 1])->where('set_date','<=','2019-06-01')->get();
	
	    $financialListObj = new FinancialController();
	    //add && update
	    $financialList->map(function ($v) use($financialListObj){
	    	//save financialList
	    	$v->status = 2;
	    	$v->save();
	    	$v->refresh();
	    
	    	//calculat exchangeProfit
	    	$exchangeProfitMoney = $financialListObj->exchangeMoney($v)->profit;
		    
	    	$oldCreated_at = new \DateTime($v->set_date);
	    	$oldCreated_at->modify('+2 month');
	    	
	    	$financial_lists_id = $v->id;
	    	$bonusReach = isset($v->bonus) ? $v->bonus->bonusReach : [];
		    
	    	$bonusId = $bonusReach->bonus_id ?? 0;
		    
	    	$reachRate = $bonusReach->reach_rate ?? 0;
		   
	    	$provideMoney = $exchangeProfitMoney * $reachRate / 100;
		    
	    
	    	$provide = Provide::where('financial_lists_id', $financial_lists_id)->first();
	    
	    	$provideData = [
	    	 'bonus_id' => $bonusId,
	    	 'financial_lists_id' => $financial_lists_id,
	    	 'provide_money' => $provideMoney > 0 ? $provideMoney  : 0
	    	];
	    
	    	if (isset($provide)) {
	    		//update
	    		foreach ($provideData as $key => $item) {
	    			$provide->$key = $item;
	    		}
	    		$provide->save();
	    	} else {
	    		//new
	    		$provideData['created_at'] = $oldCreated_at->format('Y-m-01 H:i:s');
	    		$provideData['updated_at'] = $oldCreated_at->format('Y-m-01 H:i:s');
			    $oldCreated_at->modify('-1 month');
	        $v->receipt->update(['created_at'=> $oldCreated_at->format('Y-m-01 H:i:s'),'updated_at'=> $oldCreated_at->format('Y-m-01 H:i:s')]);
	    		Provide::create($provideData);
	    	}
	    
	    });
    }
}

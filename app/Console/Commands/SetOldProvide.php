<?php

namespace App\Console\Commands;
ini_set('max_execution_time', 300);
use App\FinancialReceipt;
use App\FinancialList;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\UserController;
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
	    $financialList = FinancialList::with('receipt')->where(['status' => 1])->where('set_date','<=','2019-06-01')->get();
	    $erpUserDatas = new UserController();
	    $erpUserDatas->getErpUser();
	    $leavelUser = collect($erpUserDatas->users)->where('user_resign_date','!=','0000-00-00');
			
	    $financialListObj = new FinancialController();
	   
	    //add && update
	    $financialList->map(function ($v) use($financialListObj,$leavelUser){
		    
	    	////離職員工 如果收款日期在離職後 不存入已放款
	    	if($leavelUser->where('id',$v->erp_user_id)->count() > 0){
	    		$leaveDate = $leavelUser->where('id',$v->erp_user_id)->first()['user_resign_date'];
			    dd($leaveDate >= $v->receipt->created_at);
	    		if($leaveDate >= $v->receipt->created_at){
				    return;
			    }
		    }
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

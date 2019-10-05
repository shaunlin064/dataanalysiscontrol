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
	    $financialList = FinancialList::with('receipt')->where(['status' => 1])->where('set_date','<=','2019-05-01')->get();
	    $finReceiptObj = new FinancialReceipt();
	    $erpUserDatas = new UserController();
	    $erpUserDatas->getErpUser();
	    $leavelUser = collect($erpUserDatas->users)->where('user_resign_date','!=','0000-00-00');
			
	    $financialListObj = new FinancialList();
	   
	    //add && update
	    $financialList->map(function ($v) use($financialListObj,$leavelUser,$finReceiptObj){
		    
	    	////離職員工 如果收款日期在離職後 不存入已放款
	    	//if($leavelUser->where('id',$v->erp_user_id)->count() > 0){
	    	//	$leaveDate = $leavelUser->where('id',$v->erp_user_id)->first()['user_resign_date'];
	      //
	    	//	if($leaveDate <= $v->receipt->created_at){
				//    return;
			  //  }
		    //}
		    // 過往資料直接過濾至已發款
		    $finReceiptObj->checkinPassData($v);
	    	
	    });
    }
}

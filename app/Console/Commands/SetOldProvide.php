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
        $startTime = microtime(true);
        
	    $financialList = FinancialList::with('receipt')->where('set_date','<',config('custom.setOldDateLine'))->get();
	    $finReceiptObj = new FinancialReceipt();
	    
	    //add && update
	    $financialList->map(function ($v) use($finReceiptObj){
	     
		    $finReceiptObj->checkinPassData($v);
	    	
	    });
    
        $runTime = round(microtime(true) - $startTime, 2);
        echo ("Commands: {$this->signature} ({$runTime} seconds)\n");
    }
}

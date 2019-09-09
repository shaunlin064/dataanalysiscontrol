<?php

namespace App\Console\Commands;

use App\Http\Controllers\Financial\FinancialListController;
use App\Http\Controllers\FinancialController;
use Illuminate\Console\Command;

class UpdateFinancialMoneyReceipt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_financial_money_receipt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '取回目前月份已收款的cp_detail_id';

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
	    $finanicalList = new FinancialListController();
	    $finanicalList->updateFinancialMoneyReceipt();
	    
    }
}

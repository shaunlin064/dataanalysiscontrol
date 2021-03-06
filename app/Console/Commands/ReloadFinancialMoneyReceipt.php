<?php

namespace App\Console\Commands;
use App\FinancialReceipt;
use Illuminate\Console\Command;

class ReloadFinancialMoneyReceipt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reload_financial_money_receipt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '取回所有已收款的cp_detail_id';

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

	    $finanicalReceiptObj = new FinancialReceipt();
	    $finanicalReceiptObj->updateFinancialMoneyReceipt('all');

        $runTime = round(microtime(true) - $startTime, 2);
        echo ("Commands: {$this->signature} ({$runTime} seconds)\n");
    }
}

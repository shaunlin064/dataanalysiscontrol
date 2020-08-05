<?php

namespace App\Console\Commands;

use App\FinancialReceipt;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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

    public function handle()
    {
        //
        $startTime = microtime(true);
        try {
            DB::beginTransaction();
            $finanicalReceiptObj = new FinancialReceipt();
            $finanicalReceiptObj->updateFinancialMoneyReceipt();
            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            // Handle Error
            \App\Jobs\SentMail::dispatch('crontab',['mail'=>env('NOTIFICATION_EMAIL'),'name'=>'admin', 'title' => "{$this->signature} error {$e->getMessage()}"]);
        }

        $runTime = round(microtime(true) - $startTime, 2);
        echo ("Commands: {$this->signature} ({$runTime} seconds)\n");
    }
}

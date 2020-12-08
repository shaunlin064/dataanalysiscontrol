<?php

namespace App\Console\Commands;

use App\FinancialList;
use App\Service\DisturbDataService;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class UpdateFinancialData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_financial_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每月更新業務財報資料';

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
     * @throws \Exception
     */
    public function handle()
    {
        //
        try {
            DB::beginTransaction();
            $startTime = microtime(true);
            Artisan::Call('cache_erp_datas');
            $finanicalList = new FinancialList();

            $datetime = new DateTime(date('Ym01'));
            $nowdate = new DateTime();
            /*last month*/
            if($nowdate->format('d') < 16){
                $datetime->modify('-1Month');
                $finanicalList->saveCloseData($datetime->format('Ym01'));
            }
            /*this month*/
            $finanicalList->saveCloseData($nowdate->format('Ym01'));

            /*next month*/
            $dateNextMonth = new DateTime(date('Ym01'));
            $dateNextMonth->modify('+1Month');
            $finanicalList->saveCloseData($dateNextMonth->format('Ym01'));

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            /*mail notice Job*/
            \App\Jobs\SentMail::dispatch('crontab',['mail'=>env('NOTIFICATION_EMAIL'),'name'=>'admin', 'title' => "{$this->signature} error {$e->getMessage()}"]);
            
        }

        Artisan::call('update_financial_money_receipt');
        Artisan::call('update_financial_money_receipt_cost');
        
        if(env('APP_DEMO')){
	        $disturbDataService = new DisturbDataService();
	        $disturbDataService->customer('today');
	        $disturbDataService->campaign('today');
	        $disturbDataService->financialNumber('today');
        }
        
        Artisan::call('cache_all');
        $runTime = round(microtime(true) - $startTime, 2);
        echo ("Commands: {$this->signature} ({$runTime} seconds)\n");

    }
}

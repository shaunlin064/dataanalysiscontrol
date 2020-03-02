<?php

namespace App\Console\Commands;

use App\FinancialList;
use DateTime;
use Illuminate\Console\Command;

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
        $startTime = microtime(true);
        
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
        
        $runTime = round(microtime(true) - $startTime, 2);
        echo ("Commands: {$this->signature} ({$runTime} seconds)\n");
    
        /*mail notice Job*/
        \App\Jobs\SentMail::dispatch('crontab',['mail'=>'shaun@js-adways.com.tw','name'=>'admin', 'title' => 'update_financial_data schedule down']);
    }
}

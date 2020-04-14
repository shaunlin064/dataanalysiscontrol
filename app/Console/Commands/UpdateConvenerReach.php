<?php

namespace App\Console\Commands;

use App\ExchangeRate;
use App\Jobs\SentMail;
use App\Jobs\UpdateExchange;
use App\SaleGroupsReach;
use DateTime;
use Illuminate\Console\Command;

class UpdateConvenerReach extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_convener_reach';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新設定上月所有達標招集人獎金資料 供獎金發放使用';

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

	    $date = new DateTime(date('Ym01'));
	    $setDate = $date->modify('-1Month')->format('Y-m-d');

        if(!ExchangeRate::checkDataExsist($setDate,"USD")){
            /*mail notice Job*/
            SentMail::dispatch('crontab',['mail'=>'shaun@js-adways.com.tw','name'=>'admin', 'title' => 'update_convener_reach 更新失敗沒有該月匯率資料']);
            //加入隊列
            /*重新更新匯率 重新更資資料*/
            UpdateExchange::dispatch()->delay(now()->addHour(10));
            UpdateConvenerReach::dispatch()->delay(now()->addHour(10)->addMinute(10));
            die;
        }

	    $saleGroupsReach = new SaleGroupsReach();
	    $saleGroupsReach->setAllConvenerReach($setDate);

        $runTime = round(microtime(true) - $startTime, 2);
        echo ("Commands: {$this->signature} ({$runTime} seconds)\n");

        /*mail notice Job*/
        SentMail::dispatch('crontab',['mail'=>'shaun@js-adways.com.tw','name'=>'admin', 'title' => 'update_convener_reach schedule down']);
    }
}

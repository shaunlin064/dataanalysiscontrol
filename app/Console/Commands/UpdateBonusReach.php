<?php

namespace App\Console\Commands;

use App\ExchangeRate;
use App\Http\Controllers\Bonus\BonusReachController;
use App\Jobs\SentMail;
use App\Jobs\UpdateExchange;
use Illuminate\Console\Command;

class UpdateBonusReach extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_bonus_reach';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        
	    $bonusReach = new BonusReachController();
	    $date_now =  new \DateTime();
	    $date_now->modify('-1Month');
    
        if(!ExchangeRate::checkDataExsist(now()->setDays(1)->subMonth()->format('Y-m-d'),"USD")){
            /*mail notice Job*/
            SentMail::dispatch('crontab',['name'=>'admin', 'title' => 'update_bonus_reach 更新失敗沒有該月匯率資料']);
            //加入隊列
            /*重新更新匯率 重新更資資料*/
            UpdateExchange::dispatch()->delay(now()->addHour(10));
            \App\Jobs\UpdateBonusReach::dispatch()->delay(now()->addHour(10)->addMinute(10));
            die;
        }
        
	    $bonusReach->update($date_now->format('Y-m-01'));
    
        $runTime = round(microtime(true) - $startTime, 2);
        echo ("Commands: {$this->signature} ({$runTime} seconds)\n");
    
        /*mail notice Job*/
        \App\Jobs\SentMail::dispatch('crontab',['name'=>'admin', 'title' => 'update_bonus_reach schedule down']);
    }
}

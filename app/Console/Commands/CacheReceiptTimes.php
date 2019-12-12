<?php

namespace App\Console\Commands;

use App\Http\Controllers\FinancialController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Console\Command;

class CacheReceiptTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache_receipt_times';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '快取客戶發票數量';

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
    
        $erpFin = new FinancialController();
        $results = collect($erpFin->getReciptTimes());
//
        $receiptCachedDateMonth = new \DateTime();
        $receiptCachedDateMonth->modify('-2Month');
        $receiptCachedDateMonth = $receiptCachedDateMonth->format('Ym');
    
        $results->groupBy('datemonth')->each(function($v,$date) use($receiptCachedDateMonth){
            if($date <= $receiptCachedDateMonth){
                Cache::store('memcached')->forever('receiptTimes'.$date, $v);
            } else{
                Cache::store('memcached')->put('receiptTimes'  . $date, $v,( 1 * 3600 ));
            }
        });
        
        $runTime = round(microtime(true) - $startTime, 2);
        echo ("Commands: {$this->signature} ({$runTime} seconds)\n");
    }
}

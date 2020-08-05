<?php

namespace App\Console\Commands;

use App\Cachekey;
use App\FinancialList;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateFinancialMoneyReceiptCost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_financial_money_receipt_cost';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '將純成本財報 歸入已確認狀態 供發放獎金';

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
        $startTime = microtime(true);
        $date = new DateTime(date('Ym01'));
        $date->modify('-1Month');
        //抓取 有僅填成本 且未歸入已收款的成本
        $financial = FinancialList::doesntHave('receipt')->where('income',0)->where('cost','!=',0)->where('set_date','<=',$date->format('Y-m-d'))->get();
        //建立financialReceipt

        try {
            DB::beginTransaction();
            $financial->each(function($v){
                $v->receipt()->create([
                    'created_at'         => now()->format('Y-m-d H:i:s')
                ]);
            });
            //更改financial status
            // 排除已經收放款的資料
            // 取出需要資料的設定年月份 來做清除快取使用
            $needReleaseDate = $financial->reject(function($v){
                return $v->status > 0;
            })->values()->map(function($v){
                $v->status = 1;
                $v->save();
                return $v->set_date;
            })->unique()->values();

            //刪除快取 financial_list 與 provide.list
            $needDeleteCache = Cachekey::where('type','financial.review')->whereIn('set_date',$needReleaseDate->toArray())->get();
            Cachekey::releaseCacheByDatas($needDeleteCache);

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

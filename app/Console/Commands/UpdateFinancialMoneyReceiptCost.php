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
	    //僅填成本 尚未有收款過的資料
	    $fin = FinancialList::doesntHave('receipt')->where(['income' => 0 , 'status' => 0 ])->where('cost','!=' , 0)->get();
	
	    // 比對 確認該案件 是否已經有收過款
	    $alreadyReceiveMoneyCampaignIds = FinancialList::whereIn('campaign_id',$fin->pluck('campaign_id')->unique()->values())->where('status','!=',0)->get()->pluck('campaign_id')->unique()->values();
	
	    $financial = $fin->wherein('campaign_id',$alreadyReceiveMoneyCampaignIds)->values();

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
	        $needDeleteCache = Cachekey::where('type','financial.review')->whereIn('set_date',['2020-10-01','2020-11-01'])->orWhere('type','provide.list')->get();
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

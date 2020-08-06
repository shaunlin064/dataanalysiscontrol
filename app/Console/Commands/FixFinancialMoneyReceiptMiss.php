<?php

namespace App\Console\Commands;

use App\Cachekey;
use App\FinancialList;
use App\Http\Controllers\FinancialController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixFinancialMoneyReceiptMiss extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix_financial_money_receipt_miss';

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


    public function handle()
    {

        $financial = new FinancialController();

        /*cp_detail_id and balance date */
        $receiptData = collect($financial->getBalancePayMentData('all'));
        $receiptDataCpIds = $receiptData->pluck('cp_detail_id');

        $needUpdateFinancialList = FinancialList::doesntHave('receipt')->whereIn('cp_detail_id', $receiptDataCpIds)
                                                ->where('status','>',0)
                                                ->get();
        try {
            DB::beginTransaction();
            $needUpdateFinancialList->each(function ( $v ) use (
                $receiptData
            ) {
                $balance_date = $receiptData->where('cp_detail_id', $v->cp_detail_id)
                                            ->first()['balance_date'] ?? '0000-00-00 00:00:00';
                $v->receipt()->create([
                    'created_at'         => $balance_date
                ]);
            });

            /*cache release*/
            $financial = new FinancialList();
            $needReleaseDates = $financial->whereIn('cp_detail_id', $needUpdateFinancialList->pluck('cp_detail_id'))
                                          ->pluck('set_date')
                                          ->unique()
                                          ->values();


            $releaseData = Cachekey::where('type','financial.review')->whereIn( 'set_date' ,$needReleaseDates)->orWhere('type','provide.list')->get();

            Cachekey::releaseCacheByDatas($releaseData);

            DB::commit();

        } catch (\Exception $ex) {
            DB::rollback();
            echo $ex->getMessage();
            die;
        }



    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class FixProvideError extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix_provide_error';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '修正獎金發放的%與實際財報計算的獎金級距不符';

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
        $cacheobj = new CacheKey('file');
        $financialObj = new FinancialList();
        $missData = [];
        $cacheobj->where('type', 'financial.review')->where('set_date', '>=', '2019-09-01')->get()->each(function ( $v
        ) use ( $financialObj, &$missData ) {
            $tmpData = $v->getCacheData();
            if(isset($tmpData['progress_list'])){
                foreach ( $tmpData['progress_list'] as $item ) {
                    $perUserData = $financialObj->where('status', '>', 0)
                                                ->where('erp_user_id', $item['erp_user_id'])
                                                ->where('set_date', $item['set_date'] . '-01')
                                                ->get();
                    if ( $perUserData->count() > 0 ) {
                        if ( $perUserData[0]->bonus->bonusReach ) {
                            if ( $item['bonus_rate'] != $perUserData[0]->bonus->bonusReach['reach_rate'] ) {
                                $missData[ $perUserData[0]['erp_user_id'] ][ $item['set_date'] ]['data'] = $perUserData;
                                $missData[ $perUserData[0]['erp_user_id'] ][ $item['set_date'] ]['real_bonus_rate'] = $item['bonus_rate'];
                                $missData[ $perUserData[0]['erp_user_id'] ][ $item['set_date'] ]['error_bonus_rate'] = $perUserData[0]->bonus->bonusReach['reach_rate'];
                            }
                        }
                    }
                }
            }
        });

        $outData = [];
        $missData->each(function($v,$erp_user_id) use(&$outData){
            foreach($v as $financialDatas){
                // 1. 修改bonusReach 成正確 %數
                $bonusReach = $financialDatas['data'][0]->bonus->bonusReach;
                $bonusReach->reach_rate =  $financialDatas['real_bonus_rate'];
                $bonusReach->update();

                foreach($financialDatas['data'] as $item) {
                    //2. 暫存outData
                    $outData[$financialDatas['data'][0]->user->name][] = [
                        'campaign_name' => $item['campaign_name'],
                        'set_date' => $item['set_date'],
                        'profit' => $item['profit'],
                        'real_bonus_rate' => $financialDatas['real_bonus_rate'],
                        'error_bonus_rate' => $financialDatas['error_bonus_rate'],
                        'real_provide' => round($item['profit'] * $financialDatas['real_bonus_rate'] / 100),
                        'error_provide' => $item->provide['provide_money'] ?? 0
                    ];

                    // 2. 逐筆修改 financial_provide provide_money 成正確金額
                    // financial['profit'] * 正確 %數 存成 provide_money 儲存
                    //
                    //如果已經發獎金的話
                    $provide = $item->provide;
                    if($provide){

                        $provide['provide_money'] = round($item['profit'] * $financialDatas['real_bonus_rate'] / 100);
                        $provide->update();
                    }

                }
            }
        });
        $temp[] = [
            '姓名',
            '案件名稱',
            '財報月份',
            '毛利',
            '錯誤獎金%',
            '正確獎金%',
            '錯誤獎金',
            '正確獎金',
        ];
        foreach ($outData as $userName => $items){
            foreach ($items as $item ){
                $temp[] = [
                    $userName,
                    $item['campaign_name'],
                    $item['set_date'],
                    $item['profit'],
                    $item['error_bonus_rate'],
                    $item['real_bonus_rate'],
                    $item['error_provide'],
                    $item['real_provide'],
                ];
            }
        }

        return view('exports.table', [
            'datas' => $outData
        ]);
    }
}

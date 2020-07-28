<?php

namespace App\Console\Commands;

use App\Bonus;
use App\FinancialList;
use App\FinancialReceipt;
use App\Provide;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UpdatePassDatas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_pass_datas';

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
        $erpUSerId = Bonus::all()->pluck('erp_user_id')->unique()->values();

        $bonuslist = FinancialList::whereNotIn('erp_user_id',$erpUSerId )
                                  ->get();

        ///這些之前的後勤與離職業務直接歸入已放款
        $finReceiptObj = new FinancialReceipt();
        //add && update
        $bonuslist->map(function ($v) use($finReceiptObj){

            $finReceiptObj->providOldData($v);

        });

//        $createdTime = new DateTime();
//        if ( $createdTime->format('d') >= 6 ) {
//            $createdTime->modify('+1Month');
//        }

//        $financialList = $bonuslist;
        //add && update
//        $financialList->map(function ( $v ) use ( $createdTime,$fincialList ) {
//            //save financialList
//            $v->status = 2;
//            $v->save();
//            $v->refresh();
//
//            //calculat exchangeProfit
//            $exchangeProfitMoney = $fincialList->exchangeMoney($v)->profit;
//
//            $financial_lists_id = $v->id;
//            $bonusReach = isset($v->bonus) ? $v->bonus->bonusReach : [];
//            $bonusId = $bonusReach->bonus_id ?? 0;
//            $reachRate = $bonusReach->reach_rate ?? 0;
//            $provideMoney = $exchangeProfitMoney * $reachRate / 100;
//
//
//            $provide = Provide::where('financial_lists_id', $financial_lists_id)->first();
//
//            $provideData = [
//                'bonus_id'           => $bonusId,
//                'financial_lists_id' => $financial_lists_id,
//                'provide_money'      => $provideMoney,
//                'created_at'         => $createdTime->format('Y-m-01'),
//            ];
//
//            if ( isset($provide) ) {
//                $provide->update($provideData);
//            } else {
//                //new
//                Provide::create($provideData);
//            }
//
//        });



        /*設定責任額*/
        $userdata = $bonuslist->pluck('erp_user_id')->unique()->values()->map(function($v,$k){
            $new = [
                'erp_user_id' => $v,
                'name' => Cache::store('file')->get('users')[$v]['name'],
                'boundary' => 0
            ];
            return $new;
        });

        $bonusLevels = [
            0 => [
                "achieving_rate" => "30",
                "bonus_rate" => "5",
                'bonus_direct' => 0,
            ],
            1 => [
                "achieving_rate" => "50",
                "bonus_rate" => "7",
                'bonus_direct' => 0,
            ],
            2 => [
                "achieving_rate" => "80",
                "bonus_rate" => "9",
                'bonus_direct' => 0,
            ],
            3 => [
                "achieving_rate" => "100",
                "bonus_rate" => "9",
                'bonus_direct' => 15000,
            ],
            4 => [
                "achieving_rate" => "150",
                "bonus_rate" => "9",
                'bonus_direct' => 20000,
            ]
        ];
        $nextMonth = date('Y-m-01',strtotime("+3 month"));

        foreach($userdata as $importData) {

            $dateStart = new \DateTime('2018-01-01');
            while($nextMonth != $dateStart->format('Y-m-01')) {
                $importData['set_date'] =$dateStart->format('Y-m-01');
                $bonus = Bonus::create($importData);
                $setBonusLevels = $bonusLevels;
                $bonus->levels()->createMany($setBonusLevels);
                $dateStart = $dateStart->modify('+1 Month');
                $groupData = $importData;
                unset($groupData['boundary']);
                if(in_array($importData['erp_user_id'],[129,102,36,164])){
                    $groupData['sale_groups_id'] = 4;
                }else{
                    $groupData['sale_groups_id'] = 5;
                }

                \App\SaleGroupsUsers::create($groupData);
            }
        }
        $runTime = round(microtime(true) - $startTime, 2);
        echo ("Commands: {$this->signature} ({$runTime} seconds)\n");
    }
}

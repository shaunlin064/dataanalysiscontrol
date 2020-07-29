<?php


    namespace App;

    use App\Http\Controllers\FinancialController;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;

    class FinancialReceipt extends Model {
        //
        protected $fillable = [ 'financial_lists_id', 'created_at', 'updated_at' ];

        public function updateFinancialMoneyReceipt ( $type = 'select' ) {
            $financial = new FinancialController();

            /*cp_detail_id and balance date */
            $receiptData = collect($financial->getBalancePayMentData($type));
            $receiptDataCpIds = $receiptData->pluck('cp_detail_id');

            $needUpdateFinancialList = FinancialList::whereIn('cp_detail_id', $receiptDataCpIds)
//                                                    ->where('status', 0)
                                                    ->get()
                                                    ->filter(function ( $v ) use ( $receiptData ) {
                                                        $balance_date = $receiptData->where('cp_detail_id',
                                                                $v->cp_detail_id)
                                                                                    ->first()['balance_date'] ?? '0000-00-00 00:00:00';
                                                        return $v['set_date'] < date("Y-m-d", strtotime($balance_date));
                                                    })
                                                    ->values();

            /*建立 financialReceipt 未存在才建立*/
            $needUpdateFinancialList = $needUpdateFinancialList->reject(function ( $v ) {
                return $this->where('financial_lists_id', $v->id)->exists();
            });

            try {
                DB::beginTransaction();
                $needUpdateFinancialList->each(function ( $v ) use (
                    $receiptData
                ) {
                    $balance_date = $receiptData->where('cp_detail_id', $v->cp_detail_id)
                                                ->first()['balance_date'] ?? '0000-00-00 00:00:00';
                    $this->create([
                        'financial_lists_id' => $v->id,
                        'created_at'         => $balance_date
                    ]);
                });

                /*更新收款狀態*/
                FinancialList::whereIn('cp_detail_id', $needUpdateFinancialList->pluck('cp_detail_id'))
                             ->where('status', 0)
                             ->update([ 'status' => 1 ]);

                /*cache release*/
                $financial = new FinancialList();
                $needReleaseDates = $financial->whereIn('cp_detail_id', $needUpdateFinancialList->pluck('cp_detail_id'))
                                              ->pluck('set_date')
                                              ->unique()
                                              ->values();


                $releaseData = Cachekey::where('type','financial.review')->whereIn( 'set_date' ,$needReleaseDates)->orWhere('type','provide.list')->get();

                Cachekey::releaseCacheByDatas($releaseData);

                DB::commit();

            } catch (Exception $ex) {
                DB::rollback();
                echo $ex->getMessage();
            }


        }

        public function checkinPassData ( FinancialList $v ) {
            $nowAvalibelUser = [
                'ids'     => [ 67, 84, 131, 132, 133, 136, 153, 170, 174, 181, 186, 188, 200, 201, 204, 205 ],
                'setDate' => config('custom.setOldDateLine')
            ];
            $leaveUser1 = [
                'ids'     => [ 97, 175 ],
                'setDate' => '2019-03-01'
            ]; //3月以前不用
            $leaveUser2 = [
                'ids'     => [ 156, 161 ],
                'setDate' => '2019-04-01'
            ]; //4月以前不用
            $leaveUser3 = [
                'ids'     => [ 122 ],
                'setDate' => '2019-07-01'
            ]; //7月以前不用
            $leaveUser4 = [
                'ids'     => [ 155 ],
                'setDate' => '2019-08-01'
            ]; //8月以前不用
            if ( in_array($v->erp_user_id, $leaveUser1['ids']) && $v->set_date < $leaveUser1['setDate'] ) {
                $this->providOldData($v);
            } else if ( in_array($v->erp_user_id, $leaveUser2['ids']) && $v->set_date < $leaveUser2['setDate'] ) {
                $this->providOldData($v);
            } else if ( in_array($v->erp_user_id, $leaveUser3['ids']) && $v->set_date < $leaveUser3['setDate'] ) {
                $this->providOldData($v);
            } else if ( in_array($v->erp_user_id, $leaveUser4['ids']) && $v->set_date < $leaveUser4['setDate'] ) {
                $this->providOldData($v);
            } else if ( in_array($v->erp_user_id,
                    $nowAvalibelUser['ids']) && $v->set_date < $nowAvalibelUser['setDate'] ) {
                $this->providOldData($v);
            }
        }

        public function providOldData ( FinancialList $finListObj ) {
            //save financialList
            $finListObj->status = 2;
            $finListObj->save();

            //calculat exchangeProfit
            $exchangeProfitMoney = $finListObj->exchangeMoney($finListObj)->profit;

            $oldCreated_at = new \DateTime($finListObj->set_date);
            $oldCreated_at->modify('+4 month');

            $financial_lists_id = $finListObj->id;
            $bonusReach = isset($finListObj->bonus) ? $finListObj->bonus->bonusReach : [];

            $bonusId = $bonusReach->bonus_id ?? 0;

            $reachRate = $bonusReach->reach_rate ?? 0;

            $provideMoney = $exchangeProfitMoney * $reachRate / 100;

            $provide = Provide::where('financial_lists_id', $financial_lists_id)->first();

            $provideData = [
                'bonus_id'           => $bonusId,
                'financial_lists_id' => $financial_lists_id,
                'provide_money'      => $provideMoney
            ];

            if ( isset($provide) ) {
                //update
                foreach ( $provideData as $key => $item ) {
                    $provide->$key = $item;
                }
                $provide->save();
            } else {
                //new
                $provideData['created_at'] = $oldCreated_at->format('Y-m-01 H:i:s');
                $provideData['updated_at'] = $oldCreated_at->format('Y-m-01 H:i:s');
                $oldCreated_at->modify('-1 month');

                if ( isset($finListObj->receipt) ) {
                    $finListObj->receipt->update([
                        'created_at' => $oldCreated_at->format('Y-m-01 H:i:s'),
                        'updated_at' => $oldCreated_at->format('Y-m-01 H:i:s')
                    ]);
                }

                Provide::create($provideData);
            }
        }
    }

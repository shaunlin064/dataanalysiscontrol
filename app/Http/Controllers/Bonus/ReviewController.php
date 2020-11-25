<?php
    /**
     * Created by PhpStorm.
     * User: shaun
     * Date: 2019-06-20
     * Time: 10:15
     */


    namespace App\Http\Controllers\Bonus;

    use App\Bonus;
    use App\Cachekey;
    use App\CacheKeySub;
    use App\CustomerGroups;
    use App\FinancialList;
    use App\Http\Controllers\BaseController;
    use App\Http\Controllers\Financial\ProvideController;
    use App\Http\Controllers\FinancialController;
    use App\SaleGroups;
    use App\User;
    use Auth;
    use DateTime;
    use Exception;
    use Gate;
    use Illuminate\Http\Request;
    use Illuminate\Support\Collection;
    use Illuminate\Support\Facades\Cache;

    class ReviewController extends BaseController {
        //
        protected $policyModel;
        protected $cacheObj;
        protected $cacheKey = 'financial.review';

        public function __construct () {

            parent::__construct();

            $this->policyModel = new FinancialList();
        }

        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         * @throws Exception
         */
        public function view () {

            $loginUserId = auth()->user()->erp_user_id;

            $date = new DateTime();

            $objFin = new FinancialList();

            $agencyList = $objFin->getDataList('agency_name', 'agency_id');
            $clientList = $objFin->getDataList('client_name', 'client_id');
            $mediaCompaniesList = $objFin->getDataList('companies_name', 'companies_id');
            $medias = $objFin->getDataList('media_channel_name', 'media_channel_name');

            $provideObj = new ProvideController();
            [ $saleGroups, $userList ] = $provideObj->getDataList($loginUserId, $date,'');

            //            $allYearProfit = $this->getAllYearProfit($userList);

            $customerProfitColumns = [
                [
                    'data'   => 'name',
                    'render' => '<a class="point customer_chart_link" data-id="${row.id}" data-type="${row.customer_type}">${data}</a>',
                ],
                [
                    'data'   => 'type',
                    'render' => '<span class="badge bg-${style}">${data}</span>',
                    'parmas' => 'let style ="red"; if(data == "直客"){ style = "blue"}'
                ],
                [ 'data' => 'receipt_times' ],
                [ 'data' => 'income' ],
                [ 'data' => 'cost' ],
                [ 'data' => 'profit' ],
                [ 'data' => 'profit_percenter' ]
            ];
            $customerGroupProfitColumns = [
                [ 'data' => 'name' ],
                [ 'data' => 'receipt_times' ],
                [ 'data' => 'income' ],
                [ 'data' => 'cost' ],
                [ 'data' => 'profit' ],
                [ 'data' => 'profit_percenter' ]
            ];
            $mediasProfitColumns = [
                [ 'data' => 'name' ],
                [
                    'data'   => 'sales_channel',
                    'render' => '<span class="badge bg-${style}">${data}</span>',
                    'parmas' => 'let style ="yellow"; if(data == "BR"){ style = "green"}else if(data == "EC"){ style = "purple"}'
                ],
                [ 'data' => 'income', 'render' => '${Math.round(data).toLocaleString("en-US")}', ],
                [ 'data' => 'cost', 'render' => '${Math.round(data).toLocaleString("en-US")}', ],
                [ 'data' => 'profit', 'render' => '${Math.round(data).toLocaleString("en-US")}', ],
                [ 'data' => 'profit_percenter' ]
            ];
            $mediaCompaniesProfitColumns = [
                [ 'data' => 'name' ],
                [ 'data' => 'income' ],
                [ 'data' => 'cost' ],
                [ 'data' => 'profit' ],
                [ 'data' => 'profit_percenter' ]
            ];
            $progressColumns = [
                [ 'data' => 'set_date', "width" => "50px" ],
                [ 'data' => 'user_name', "width" => "50px" ],
                [ 'data' => 'sale_group_name', "width" => "50px" ],
                [ 'data' => 'totalProfit' ],
                [
                    'data'   => 'bonus_next_percentage',
                    'render' => '<span class="badge bg-${style}">${data}%</span>',
                    'parmas' => 'let style ="yellow"; if(data > 90){ style = "green"}else if(data < 0){ style = "red"}',
                    "width"  => "50px"
                ],
                [ 'data' => 'bonus_rate', 'render' => '${data}%' ],
                [ 'data' => 'profit' ],
                [ 'data' => 'bonus_direct' ]
            ];
            $progressTotalColumns = [
                [ 'data' => 'user_name', "width" => "50px" ],
                [ 'data' => 'sale_group_name', "width" => "50px" ],
                [ 'data' => 'total_profit' ],
                [
                    'data'   => 'bonus_percentage_average',
                    'render' => '<span class="badge bg-${style}">${data}%</span>',
                    'parmas' => 'let style ="yellow"; if(data > 90){ style = "green"}else if(data < 0){ style = "red"}',
                    "width"  => "50px"
                ],
                [ 'data' => 'total_boundary' ],
            ];
            $groupsProgressColumns = [
                [ 'data' => 'set_date' ],
                [ 'data' => 'name' ],
                [ 'data' => 'profit' ],
                [
                    'data'   => 'percentage',
                    'render' => '<span class="badge bg-${style}">${data}%</span>',
                    'parmas' => 'let style ="yellow"; if(data > 90){ style = "green"}else if(data < 0){ style = "red"}'
                ],
            ];
            $groupsProgressTotalColumns = [
                [ 'data' => 'name' ],
                [ 'data' => 'total_profit' ],
                [
                    'data'   => 'percentage_average',
                    'render' => '<span class="badge bg-${style}">${data}%</span>',
                    'parmas' => 'let style ="yellow"; if(data > 90){ style = "green"}else if(data < 0){ style = "red"}'
                ],
                [ 'data' => 'total_boundary' ],
            ];
            $bonusColumns = [
                [ 'data' => 'set_date' ],
                [ 'data' => 'user_name' ],
                [ 'data' => 'sale_group_name' ],
                [
                    'data'   => 'campaign_name',
                    'render' => sprintf('<a href="http://%s/jsadwaysN2/campaign_view.php?id=${row.campaign_id}" target="_blank">${row.campaign_name}</a>',
                        env('ERP_URL'))
                ],
                [ 'data' => 'media_channel_name' ],
                [ 'data' => 'sell_type_name' ],
                [ 'data' => 'currency' ],
                [ 'data' => 'organization' ],
                [ 'data' => 'income' ],
                [ 'data' => 'cost' ],
                [
                    'data'   => 'profit',
                    'render' => '<p style="${style}">${row.profit}</p>',
                    'parmas' => 'let style = row.organization == "hk" ? "color:red" : "" '
                ],
                [ 'data' => 'paymentStatus' ],
                [ 'data' => 'bonusStatus' ],
            ];
//
//            if(auth()->user()->name === 'shaun'){
//                /*ajax check debug*/ //
//
//                $dateStart = $date->format('2020-07-01');
//                $dateEnd = $date->format('2020-07-01');
//                $request = new Request([
//                    'startDate'    => $dateStart,
//                    'endDate'      => $dateEnd,
//                    'saleGroupIds' => [ '1','2','3','4','5','6','7','8' ],
//                    'userIds'      => [],
//                    'agencyIdArrays' => [],
//                    'clientIdArrays' => [],
//                    'mediaCompaniesIdArrays' => [],
//                    'mediasNameArrays' => []
//                ]);
//                $return = $this->getAjaxData($request, 'return');
//                dd($return);
//            }

            return view('bonus.review.view', [
                'data'                        => $this->resources,
                'bonusColumns'                => $bonusColumns,
                'progressColumns'             => $progressColumns,
                'progressTotalColumns'        => $progressTotalColumns,
                'groupProgressColumns'        => $groupsProgressColumns,
                'groupProgressTotalColumns'   => $groupsProgressTotalColumns,
                'saleGroups'                  => $saleGroups,
                'clientList'                  => $clientList,
                'agencyList'                  => $agencyList,
                'mediaCompaniesList'          => $mediaCompaniesList,
                'medias'                      => $medias,
                'userList'                    => $userList,
                'customerProfitColumns'       => $customerProfitColumns,
                'mediasProfitColumns'         => $mediasProfitColumns,
                'mediaCompaniesProfitColumns' => $mediaCompaniesProfitColumns,
                //                    'allYearProfit' => $allYearProfit,
                'customerGroupProfitColumns'  => $customerGroupProfitColumns,
            ]);
        }

        /**
         * @param Request $request
         * @param string $outType
         * @return array
         * @throws \Psr\SimpleCache\InvalidArgumentException
         * @throws Exception
         */
        public function getAjaxData ( Request $request, $outType = 'echo' ) {

            [
                $progress_list,
                $tmpGroupProgressList,
                $progress_list_total,
                $bonus_list,
                $chartMoneyStatus,
                $chartFinancialBar,
                $chartFinancialBarLastRecord,
                $customerPrecentageProfit,
                $customerProfitData,
                $customerGroupsProfitData,
                $mediaCompaniesProfitData,
                $mediasProfitData,
                $saleChannelProfitData,
                $chart_bar_max_y,
                $bonusCharBarStack,
                $group_progress_list_total
            ] = $this->getCacheDatas($request);

            /*$bonusCharBarStack*/
            $bonus_list = $bonus_list->map(function ( $v, $k ) {
                $v['income'] = isset($v['income']) ? number_format($v['income']) : 0;
                $v['cost'] = isset($v['cost']) ? number_format($v['cost']) : 0;
                $v['profit'] = isset($v['profit']) ? number_format($v['profit']) : 0;
                return $v;
            })->values()->toArray();

            $progress_list = $progress_list->map(function ( $v, $k ) {
                $v['profit'] = isset($v['profit']) ? number_format($v['profit']) : 0;
                $v['totalProfit'] = isset($v['totalProfit']) ? number_format($v['totalProfit']) : 0;
                return $v;
            });
            $progress_list_total = $progress_list_total->map(function ( $v, $k ) {
                $v['total_boundary'] = isset($v['total_boundary']) ? number_format($v['total_boundary']) : 0;
                $v['total_profit'] = isset($v['total_profit']) ? number_format($v['total_profit']) : 0;
                return $v;
            });
            $group_progress_list = $tmpGroupProgressList->map(function ( $v, $k ) {
                $v['profit'] = isset($v['profit']) ? number_format($v['profit']) : 0;
                return $v;
            });

            $mediaCompaniesProfitData = collect($mediaCompaniesProfitData)->map(function($v){

                $v['income'] = isset($v['income']) ? number_format($v['income']) : 0;
                $v['cost'] = isset($v['cost']) ? number_format($v['cost']) : 0;
                $v['profit'] = isset($v['profit']) ? number_format($v['profit']) : 0;
                return $v;
            })->values()->toArray();

            $returnData = [
                "bonus_list"                      => $bonus_list,
                "chart_money_status"              => $chartMoneyStatus,
                "chart_financial_bar"             => $chartFinancialBar,
                "chart_financial_bar_last_record" => $chartFinancialBarLastRecord,
                "bonus_char_bar_stack"            => $bonusCharBarStack,
                'progress_list'                   => $progress_list,
                'progress_list_total'             => $progress_list_total,
                'group_progress_list'             => $group_progress_list,
                'group_progress_list_total'       => $group_progress_list_total,
                'customer_precentage_profit'      => $customerPrecentageProfit,
                'customer_profit_data'            => $customerProfitData,
                'medias_profit_data'              => $mediasProfitData,
                'media_companies_profit_data'     => $mediaCompaniesProfitData,
                'sale_channel_profit_data'        => $saleChannelProfitData,
                'customer_groups_profit_data'     => $customerGroupsProfitData,
                'chart_bar_max_y'                 => $chart_bar_max_y
            ];

            if ( $outType == 'echo' ) {
                echo json_encode($returnData);
            } else {
                return $returnData;
            }
        }

        /**
         * @param $uId
         * @param $totalProfit
         * @param string $yearMonthDay
         * @return array
         */
        public function getUserBonus ( $erpUserId, $totalProfit, string $yearMonthDay ): array {
            // getUserBonus
            $bonus = new Bonus();
            $returnBonusData = $bonus->getUserBonus($erpUserId, $totalProfit, $yearMonthDay);

            // set output Data
            $boxData = [
                'profit'                => $returnBonusData['estimateBonus'],
                'bonus_rate'            => isset($returnBonusData['reachLevle']['bonus_rate']) ? $returnBonusData['reachLevle']['bonus_rate'] : 0,
                'bonus_next_amount'     => isset($returnBonusData['nextLevel']['bonus_next_amount']) ? round($returnBonusData['nextLevel']['bonus_next_amount']) : 0,
                'bonus_next_percentage' => isset($returnBonusData['nextLevel']['bonus_next_percentage']) ? $returnBonusData['nextLevel']['bonus_next_percentage'] : 0,
                'bonus_direct'          => number_format($returnBonusData['bonusDirect']),
                'boundary'              => $returnBonusData['boundary']
            ];

            return $boxData;
        }

        /**
         * @param $financialDataArrays
         * @param $agencyIdArrays
         * @param $clientIdArrays
         * @param $mediaCompaniesIdArrays
         * @param $mediasNameArrays
         * @return Collection
         */
        public function getFilterData (
            $financialDataArrays, $agencyIdArrays, $clientIdArrays, $mediaCompaniesIdArrays, $mediasNameArrays
        ) {

            $financialDataArrays = collect($financialDataArrays);

            if ( !empty($agencyIdArrays) ) {
                $financialDataArrays = $financialDataArrays->whereIn('agency_id', $agencyIdArrays);
            }

            if ( !empty($clientIdArrays) ) {
                $financialDataArrays = $financialDataArrays->whereIn('agency_id', 0)
                                                           ->whereIn('client_id', $clientIdArrays);
            }

            if ( !empty($mediaCompaniesIdArrays) ) {
                $financialDataArrays = $financialDataArrays->whereIn('companies_id', $mediaCompaniesIdArrays);
            }

            if ( !empty($mediasNameArrays) ) {
                $financialDataArrays = $financialDataArrays->whereIn('media_channel_name', $mediasNameArrays);
            }

            return $financialDataArrays;
        }

        /**
         * @param $dateRangerArray
         * @return array
         * @throws \Psr\SimpleCache\InvalidArgumentException
         */
        public function getreceiptTimes ( $dateRangerArray ) {
            $erpFin = new FinancialController();
            $data = [];
            foreach ( $dateRangerArray as $dateMonth ) {
                if ( Cache::store('memcached')->has('receiptTimes' . $dateMonth) ) {
                    $data[] = Cache::store('memcached')->get('receiptTimes' . $dateMonth);
                } else {
                    $results = $erpFin->getReciptTimes($dateMonth);
                    Cache::store('memcached')->put('receiptTimes' . $dateMonth, $results, ( 24 * 3600 ));
                    $data[] = $results;
                }
            }
            return $data;
        }

        /**
         * @param $items
         * @return array
         */
        public function getNumberSum ( $items ): array {
            $income = $items->sum('income');
            $profit = $items->sum('profit');
            $cost = $items->sum('cost');

            if ( $profit < 0 && $income <= 0 ) {
                $profitPercenter = '-100%';
            } else if ( $profit > 0 && $income <= 0 ) {
                $profitPercenter = '100%';
            } else if ( $income == 0 && $profit == 0 ) {
                $profitPercenter = '0%';
            } else {
                $profitPercenter = round($profit / $income * 100, 1) . '%';
            }
            return [ $income, $profit, $cost, $profitPercenter ];
        }

        /**
         * @param array $userIds
         * @param $dateStart
         * @param $dateEnd
         * @param SaleGroups $SaleGroupsObj
         * @param $saleGroupIds
         * @return array
         * @throws Exception
         */
        private function getDataFromDataBase ( array $userIds, $saleGroupIds, $dateStart, $dateEnd ): array {
            $SaleGroupsObj = new SaleGroups();
            $erpReturnData = collect($this->getFinancialData($userIds, $dateStart, $dateEnd));
            /*progressDatas*/
            $progressDatas = $this->getProgressDates($erpReturnData, $dateStart, $dateEnd, $userIds);
            ///*get group Profit */
            $groupDateStart = new DateTime($dateStart);
            $tmpGroups = [];
            /*getGroupBoundary*/
            if ( $dateEnd == $groupDateStart->format('Y-m-01') ) {
                $tmpGroups[] = $SaleGroupsObj->getGroupBoundary($saleGroupIds, $groupDateStart->format('Y-m-01'));
            }
            while ( $dateEnd != $groupDateStart->format('Y-m-01') ) {
                $tmpGroups[] = $SaleGroupsObj->getGroupBoundary($saleGroupIds, $groupDateStart->format('Y-m-01'));
                $groupDateStart->modify('+1Month');
            }
            /*calculation profit percentage*/
            $groupProfitDatas = $this->getGroupProfitDates($tmpGroups, $erpReturnData);

            $erpReturnData = $erpReturnData->map(function ( $v, $k ) {
                $v['set_date'] = substr($v['set_date'], 0, 7);
                return $v;
            });

            return [ $erpReturnData->toArray(), $progressDatas->toArray(), $groupProfitDatas->toArray() ];
        }

        /**
         * @param $id
         * @param string $yearMonthDay
         * @return bool|mixed|string
         */
        private function getFinancialData ( array $erpUserIds, string $dateStart, string $dateEnd ) {
            $financialListObj = new FinancialList();
            return $financialListObj->getFinancialData($erpUserIds, $dateStart, $dateEnd);
        }

        /**
         * @param Collection $erpReturnData
         * @return Collection
         */
        private function getProgressDates ( Collection $erpReturnData, $dateStart, $dateEnd, $userIds ): Collection {
            $progressDatas = collect([]);
            $resignUsers = collect(Cache::get('users'))->where('user_resign_date', '!=', '0000-00-00');

            $erpReturnData->groupBy([ 'set_date', 'erp_user_id' ])->map(function ( $items, $setDate ) use (
                &$progressDatas
            ) {
                $items = $items->map(function ( $v, $erpUserId ) use ( $setDate ) {
                    $tmpData = $this->getUserBonus($erpUserId, $v->sum('profit'), $setDate);
                    $tmpData['totalProfit'] = $v->sum('profit');
                    $tmpData['sale_group_id'] = $v->max('sale_group_id');
                    $tmpData['sale_group_name'] = $v->max('sale_group_name');
                    $tmpData['user_name'] = $v->max('user_name');
                    $tmpData['set_date'] = substr($setDate, 0, 7);
                    $tmpData['erp_user_id'] = $erpUserId;
                    return $tmpData;
                })->values();
                $progressDatas = $progressDatas->concat($items);
            });
            //			/*補齊該月沒有 金額的user 資料*/
            foreach ( date_range($dateStart, $dateEnd) as $dateRange ) {
                $BonusList = Bonus::where('set_date', $dateRange)->whereIn('erp_user_id', $userIds)->get();
                $setDate = substr($dateRange, 0, 7);
                $haveValueProgressDatas = $progressDatas->where('set_date', $setDate)->pluck('erp_user_id');

                foreach ( $BonusList->whereNotin('erp_user_id', $haveValueProgressDatas) as $item ) {

                    $thisResignUser = $resignUsers->whereIn('id', $item->erp_user_id)->first();

                    if ( isset($thisResignUser['user_resign_date']) ) {
                        if ( $thisResignUser['user_resign_date'] <= $dateRange ) {
                            continue;
                        }
                    }
                    $tmpData = $this->getUserBonus($item->erp_user_id, 0, $dateRange);
                    $tmpData['totalProfit'] = number_format(0);

                    $tmpData['sale_group_name'] = $item->user->userGroups->where('set_date', $dateRange)->map(function (
                        $v
                    ) {

                        return $v->saleGroups ? $v->saleGroups->name : '';
                    })->implode(',');

                    //                    $tmpData['sale_group_name'] = $item->user->getUserGroupsName();
                    $tmpData['user_name'] = $item->user->name;
                    $tmpData['set_date'] = $setDate;
                    $tmpData['erp_user_id'] = $item->erp_user_id;
                    $tmpData['boundary'] = $item->boundary;
                    $tmpData['sale_group_id'] = $item->saleGroups->max('sale_groups_id');
                    $progressDatas[] = $tmpData;
                }
            };
            return $progressDatas;
        }

        /**
         * @param array $tmpGroups
         * @param Collection $erpReturnData
         * @return Collection
         */
        private function getGroupProfitDates ( array $tmpGroups, Collection $erpReturnData ): Collection {
            $groupProfitDatas = collect([]);

            collect($tmpGroups)->map(function ( $items, $k ) use ( &$groupProfitDatas, $erpReturnData ) {
                $items = collect($items)->map(function ( $item, $k ) use ( $erpReturnData ) {

                    $item['profit'] = round($erpReturnData->where('sale_group_id', $item['id'])
                                                          ->where('set_date', $item['set_date'])
                                                          ->sum('profit'));
                    $item['percentage'] = ( $item['profit'] == 0 || $item['boundary'] == 0 ) ? 0 : round($item['profit'] / $item['boundary'] * 100);
                    //                    $item['profit'] = number_format($item['profit']);
                    $item['set_date'] = substr($item['set_date'], 0, 7);
                    return $item;
                });
                $groupProfitDatas = $groupProfitDatas->concat($items);
            });
            return $groupProfitDatas;
        }

        /**
         * @param Collection $bonus_list
         */
        private function getCustomerAllAnalysis ( Collection $bonus_list, $dateRange ) {
            [ $customerPrecentageProfit, $customerProfitData ] = [ [], [] ];
            /*部份資料前置時有做 number_format 這邊做還原*/
            $bonus_list = $bonus_list->map(function ( $v, $k ) {
                $v['profit'] = str_replace(',', '', $v['profit']);
                $v['income'] = str_replace(',', '', $v['income']);
                $v['cost'] = str_replace(',', '', $v['cost']);
                return $v;
            });

            $dateRange = collect($dateRange)->map(function ( $v, $k ) {
                return str_replace('-', '', substr($v, 0, - 2));
            });

            $customerPrecentageProfit = $this->getCustomerProfitSum($bonus_list, $dateRange);
            $customerProfitData = $this->getCustomerReceiptTimes($bonus_list, $dateRange);
            $customerGroupsProfitData = $this->getCustomerGroupsProfit($bonus_list, $dateRange);

            return [ $customerPrecentageProfit, $customerProfitData, $customerGroupsProfitData ];
        }

        /**
         * @param Collection $bonus_list
         * @return mixed
         */
        private function getCustomerProfitSum ( Collection $bonus_list, $dateRange ) {
            $customerPrecentageProfit['date'] = $dateRange;

            $bonus_list->groupBy('set_date')->map(function ( $v, $date ) use ( &$customerPrecentageProfit ) {
                $key = $customerPrecentageProfit['date']->search(str_replace('-', '', $date));
                $customerPrecentageProfit['agency_profit'][ $key ] = $v->where('agency_id', '!=', 0)->sum('profit');

                $customerPrecentageProfit['client_profit'][ $key ] = $v->where('agency_id', '=', 0)->sum('profit');
            });
            foreach ( $customerPrecentageProfit['date'] as $key => $item ) {
                if ( !isset($customerPrecentageProfit['agency_profit'][ $key ]) ) {
                    $customerPrecentageProfit['agency_profit'][ $key ] = 0;
                    $customerPrecentageProfit['client_profit'][ $key ] = 0;
                }
            }

            if ( empty($customerPrecentageProfit['agency_profit']) ) {
                $customerPrecentageProfit['agency_profit'][] = 0;
                $customerPrecentageProfit['client_profit'][] = 0;
            }
            $customerPrecentageProfit['agency_profit'] = collect($customerPrecentageProfit['agency_profit'])->sortKeys();
            $customerPrecentageProfit['client_profit'] = collect($customerPrecentageProfit['client_profit'])->sortKeys();

            return $customerPrecentageProfit;
        }

        /**
         * @param Collection $bonus_list
         * @param Collection $dateRangerArray
         * @return mixed
         */
        private function getCustomerReceiptTimes ( Collection $bonus_list, Collection $dateRangerArray ) {
            $customerProfitData = [];

            $receiptTimesData = collect($this->getreceiptTimes($dateRangerArray))->flatten(1);

            $bonus_list->filter(function ( $v ) {
                return $v['agency_id'] != 0;
            })->groupBy('agency_id')->each(function ( $v, $id ) use ( &$customerProfitData, $receiptTimesData ) {
                if ( $receiptTimesData->count() > 0 ) {
                    $receiptAgencyTimes = $receiptTimesData->where('agency_id', $v->max('agency_id'))
                                                           ->sum('receipt_count_times');
                }

                [ $income, $profit, $cost, $profitPercenter ] = $this->getNumberSum($v);

                $customerProfitData[ $id ] = [
                    'id'               => $id,
                    'customer_type'    => 'agency',
                    'name'             => $v->max('agency_name'),
                    'type'             => '代理商',
                    'receipt_times'    => $receiptAgencyTimes ?? 0,
                    'income'           => number_format($income),
                    'cost'             => number_format($cost),
                    'profit'           => number_format($profit),
                    'profit_percenter' => $profitPercenter
                ];

                return $v;
            });

            sort($customerProfitData);

            $bonus_list->filter(function ( $v ) {
                return $v['agency_id'] == 0;
            })->groupBy('client_id')->each(function ( $v, $id ) use ( &$customerProfitData, $receiptTimesData ) {
                if ( $receiptTimesData->count() > 0 ) {
                    $receiptClientTimes = $receiptTimesData->where('client_id', $v->max('client_id'))
                                                           ->sum('receipt_count_times');
                }
                [ $income, $profit, $cost, $profitPercenter ] = $this->getNumberSum($v);

                $customerProfitData[] = [
                    'id'               => $id,
                    'customer_type'    => 'client',
                    'name'             => $v->max('client_name'),
                    'type'             => '直客',
                    'receipt_times'    => $receiptClientTimes ?? 0,
                    'income'           => number_format($income),
                    'cost'             => number_format($cost),
                    'profit'           => number_format($profit),
                    'profit_percenter' => $profitPercenter
                ];
                return $v;
            });


            return $customerProfitData;
        }

        private function getCustomerGroupsProfit ( Collection $bonus_list, $dateRange ) {
            $customerGroup = new CustomerGroups();
            $customerGroup = collect($customerGroup->getCustomerGroupDatas());
            $receiptTimesData = collect($this->getreceiptTimes($dateRange))->flatten(1);

            $customerGroupsProfit = $customerGroup->map(function ( $v, $k ) use ( $bonus_list, $receiptTimesData ) {

                $items = $bonus_list->whereIn('agency_id', $v['customer']['agency'])
                                    ->concat($bonus_list->whereIn('client_id', $v['customer']['client']));

                [ $income, $profit, $cost, $profitPercenter ] = $this->getNumberSum($items);

                $receiptTimes = $receiptTimesData->whereIn('agency_id', $v['customer']['agency'])
                                                 ->concat($receiptTimesData->whereIn('client_id',
                                                     $v['customer']['client']))
                                                 ->sum('receipt_count_times');

                $newdata = [
                    'name'             => $v['name'],
                    'receipt_times'    => $receiptTimes,
                    'income'           => number_format($income),
                    'cost'             => number_format($cost),
                    'profit'           => number_format($profit),
                    'profit_percenter' => $profitPercenter,
                ];
                return $newdata;
            });

            return $customerGroupsProfit;
        }

        /**
         * @param Collection $bonus_list
         * @return array
         */
        private function getMediaAllAnalysis ( Collection $bonus_list ): array {

            /*部份資料前置時有做 number_format 這邊做還原*/
            $bonus_list = $bonus_list->map(function ( $v, $k ) {
                $v['profit'] = str_replace(',', '', $v['profit']);
                return $v;
            });

            $saleChannelProfitData = [ 'AP' => 0, 'BR' => 0, 'EC' => 0, ];
            $bonus_list->groupBy('sales_channel')->each(function ( $v, $k ) use ( &$saleChannelProfitData ) {
                $saleChannelProfitData[ $v->max('sales_channel') ] = round($v->sum('profit'));
            });
            $saleChannelProfitData = collect($saleChannelProfitData)->sortKeys();

            $mediaCompaniesProfitData = $bonus_list->groupBy('companies_id')->map(function ( $v, $k ) {

                [ $income, $profit, $cost, $profitPercenter ] = $this->getNumberSum($v);

                $data = [
                    'name'             => $v->max('companies_name'),
                    'income'           => $income,
                    'cost'             => $cost,
                    'profit'           => $profit,
                    'profit_percenter' => $profitPercenter
                ];
                return $data;
            })->values();

            $mediasProfitData = $bonus_list->groupBy('media_channel_name')->map(function ( $v, $k ) {

                [ $income, $profit, $cost, $profitPercenter ] = $this->getNumberSum($v);

                $data = [
                    'name'             => $v->max('media_channel_name'),
                    'sales_channel'    => $v->max('sales_channel'),
                    'income'           => $income,
                    'cost'             => $cost,
                    'profit'           => $profit,
                    'profit_percenter' => $profitPercenter
                ];
                return $data;
            })->sortBy('sales_channel')->values();

            return [ $mediaCompaniesProfitData, $mediasProfitData, $saleChannelProfitData ];
        }

        /**
         * @param $date
         * @param $allUserErpIds
         * @param $allGroupIds
         * @param $cacheTime
         * @throws Exception
         */
        private function cacheDataBase (
            $date, $allUserErpIds, $allGroupIds, $cacheTime
        ) {
            if ( !$this->cacheObj->has($this->cacheKey . $date) ) {
                //
                [ $erpReturnData, $progressDatas, $groupProfitDatas ] = $this->getDataFromDataBase($allUserErpIds,
                    $allGroupIds, $date, $date);
                $this->cacheObj->put($this->cacheKey, $date, [
                    "bonus_list"          => $erpReturnData ?? [],
                    'progress_list'       => $progressDatas ?? [],
                    'group_progress_list' => $groupProfitDatas ?? []
                ], $cacheTime);
            }
        }

        /**
         * @param array $dateRange
         * @param $saleGroupIds
         * @param $userIds
         * @return array
         * @throws Exception
         */
        private function getUserDateSelectData (
            array $dateRange, $saleGroupIds, $userIds
        ): array {

            $newdata = [
                'dateAfter'  => [],
                'dateBefore' => [],
            ];


            $erpUserId = User::whereIn('id', $userIds)->get()->pluck('erp_user_id');

            // user select
            foreach ( $dateRange as $dateItem ) {

                $condition = [
                    "saleGroupIds" => $saleGroupIds,
                    "userIds"      => $userIds
                ];

                $dateAfter = $this->cacheObj->where('key', md5($this->cacheKey . $dateItem))->first();

                $tmpdatelast = new DateTime($dateItem);
                $tmpdatelast->modify('-1Year');
                $dateBefore = $this->cacheObj->where('key', md5($this->cacheKey . $tmpdatelast->format('Y-m-d')))->first();

                $needCacheDate = [];
                foreach ( [ 'dateAfter', 'dateBefore' ] as $item ) {
                    $needCacheDate[ $item ] = [];

                    $needCacheDate[ $item ]['cacheData'] = $$item->getCacheData() ?? [
                            'bonus_list'          => [],
                            'progress_list'       => [],
                            'group_progress_list' => []
                        ];
                }

                /*需要cache 資料*/ /*缺一層篩選後判斷人員要不要cache*/
                foreach ( $needCacheDate as $objKey => $item ) {
                    $tmpCondition = $condition;
                    $tmpCondition['set_date'] = $objKey === 'dateAfter' ? $dateItem : $tmpdatelast->format('Y-m-d');
                    if ( $$objKey->cacheKeySub->where('key', md5(json_encode($tmpCondition)))->count() === 0 ) {
                        $cacheData = $item['cacheData'];
                        $tmpBonus = collect([]);
                        $tmpProgressList = collect([]);
                        if ( $saleGroupIds ) {
                            /*抓出該月份select資料*/
                            $tmpBonus = $tmpBonus->concat(collect($cacheData['bonus_list'])->whereIn('sale_group_id',
                                $saleGroupIds));
                            $tmpProgressList = $tmpProgressList->concat(collect($cacheData['progress_list'])->whereIn('sale_group_id',
                                $saleGroupIds));
                        } else if ( $erpUserId ) {
                            $tmpBonus = collect($cacheData['bonus_list'])->whereIn('erp_user_id', $erpUserId);
                            $tmpProgressList = collect($cacheData['progress_list'])
                                ->whereIn('erp_user_id', $erpUserId)
                                ->values();
                        }


                        $$objKey->subPut('finance.review.userDateFilter', json_encode($tmpCondition), [
                            'bonus_list'          => $tmpBonus,
                            'progress_list'       => $tmpProgressList,
                            'group_progress_list' => collect($cacheData['group_progress_list'])
                                ->whereIn('id', $saleGroupIds)
                                ->values()
                        ]);
                    }
                }

                foreach ( [ 'dateAfter', 'dateBefore' ] as $item ) {
                    $tmpCondition = $condition;
                    $tmpCondition['set_date'] = $item === 'dateAfter' ? $dateItem : $tmpdatelast->format('Y-m-d');
                    $$item->refresh();
                    /*count user times*/
                    $$item->getCacheData();
                    $newdata[ $item ][] = $$item->cacheKeySub->where('type', 'finance.review.userDateFilter')
                                                             ->where('key', md5(json_encode($tmpCondition)))
                                                             ->first()
                                                             ->getCacheData();
                }
            }
            $tmpBonus = collect([]);
            $tmpProgressList = collect([]);
            $tmpGroupProgressList = collect([]);
            $tmpBonusLastRecord = collect([]);

            collect($newdata['dateAfter'])->map(function ( $v ) use (
                &$tmpBonus, &$tmpProgressList, &$tmpGroupProgressList
            ) {
                $tmpBonus = $tmpBonus->concat($v['bonus_list'] ?? []);
                $tmpProgressList = $tmpProgressList->concat($v['progress_list'] ?? []);
                $tmpGroupProgressList = $tmpGroupProgressList->concat($v['group_progress_list'] ?? []);

            });
            collect($newdata['dateBefore'])->map(function ( $v ) use ( &$tmpBonusLastRecord ) {
                $tmpBonusLastRecord = $tmpBonusLastRecord->concat($v['bonus_list'] ?? []);
            });

            $progress_list_total = $tmpProgressList->groupBy('erp_user_id')->map(function ( $v, $k ) {
                return [
                    'erp_user_id'              => $v->max('erp_user_id'),
                    'user_name'                => $v->max('user_name'),
                    'sale_group_name'          => $v->max('sale_group_name'),
                    'total_bonus'              => $v->sum('profit'),
                    'total_profit'             => $v->sum('totalProfit'),
                    'total_boundary'           => $v->sum('boundary'),
                    'bonus_percentage_average' => round($v->sum('bonus_next_percentage') / $v->count())
                ];
            })->values();

            return [ $tmpBonus, $tmpBonusLastRecord, $tmpProgressList, $tmpGroupProgressList, $progress_list_total ];
        }

        /**
         * @param array $dateRange
         * @throws Exception
         */
        private function cacheEachDataBase (
            array $dateRange
        ) {
            $dateNow = new DateTime();
            /*cache all user erp Id*/
            $allUserErpIds = $this->cacheObj->remember('allUserErpId', ( 1 * 3600 ), function () {
                return User::all()->pluck('erp_user_id')->toArray();
            });
            /*cache all groupId*/
            $allGroupIds = $this->cacheObj->remember('allGroupId', ( 1 * 3600 ), function () {
                return SaleGroups::all()->pluck('id')->toArray();
            });

            foreach ( $dateRange as $date ) {
                $lastRecordDate = new DateTime($date);
                $lastRecordDate->modify('-1Year');
                $lastRecordDate = $lastRecordDate->format('Y-m-d');
                /*cache 選擇資料 與 比對資料*/
                foreach ( [ $lastRecordDate, $date ] as $dateItem ) {
                     if ( !$this->cacheObj->has($this->cacheKey . $dateItem) ) {
                         $date2 = new DateTime($dateItem);
                         $dateDistance = round(( $dateNow->getTimestamp() - $date2->getTimestamp() ) / ( 3600 * 24 )); //計算資料與現在日期相差幾天
                         // 計算快取 releaseTime
                         if ( $dateDistance > 45 ) { // over 1.5 month
                             $cacheTime = 24 * 365 * 2; // 2 year
                         } else { // close one month
                             $cacheTime = 1; // 6hr
                         };
                         $this->cacheDataBase( $dateItem, $allUserErpIds, $allGroupIds, $cacheTime);
                     }
                }
            }
        }

        /**
         * @param $condition
         * @param $data
         * @return array
         * @throws Exception
         */
        private function getCustomerMediaSelectData (
            $condition, $data
        ): array {

            $startDate = $condition['startDate'];
            $endDate = $condition['endDate'];
            $agencyIdArrays = $condition['agencyIdArrays'];
            $clientIdArrays = $condition['clientIdArrays'];
            $mediaCompaniesIdArrays = $condition['mediaCompaniesIdArrays'];
            $mediasNameArrays = $condition['mediasNameArrays'];
            $tmpBonus = $data['tmpBonus'];
            $tmpBonusLastRecord = $data['tmpBonusLastRecord'];
            $tmpGroupProgressList = $data['tmpGroupProgressList'];
            $conditionJson = json_encode($condition);
            /*cache start*/
            if ( $startDate != $endDate ) {
                $dateRange = date_range($startDate, $endDate);
            }
            $dateRange[] = $condition['endDate'];


            $cacheSubObj = CacheKeySub::where('key', md5($conditionJson));

            if ( $cacheSubObj->count() === 0 ) {
                $bonus_list = $this->getFilterData($tmpBonus->toArray(), $agencyIdArrays, $clientIdArrays,
                    $mediaCompaniesIdArrays, $mediasNameArrays);
                $last_record_bonus_list = $this->getFilterData($tmpBonusLastRecord->toArray(), $agencyIdArrays,
                    $clientIdArrays, $mediaCompaniesIdArrays, $mediasNameArrays);

                $chartMoneyStatus = [
                    'unpaid' => round($bonus_list->where('status', '=', 0)->sum('income')),
                    'paid'   => round($bonus_list->where('status', '>=', 1)->sum('income'))
                ];
                $chartFinancialBar = [ 'labels' => [], 'totalIncome' => [], 'totalCost' => [], 'totalProfit' => [] ];

                foreach ( $dateRange as $dateItem ) {
                    $newtmpDate = new DateTime($dateItem);
                    $chartFinancialBar['labels'][] = $newtmpDate->format('Ym');
                    $chartFinancialBar['totalIncome'][] = 0;
                    $chartFinancialBar['totalCost'][] = 0;
                    $chartFinancialBar['totalProfit'][] = 0;
                }

                $bonus_list->groupBy('set_date')->map(function ( $v, $k ) use ( &$chartFinancialBar ) {
                    $tmpDate = new DateTime($k);
                    $key = array_search($tmpDate->format('Ym'), $chartFinancialBar['labels']);

                    $chartFinancialBar['totalIncome'][ $key ] = round($v->sum('income'));
                    $chartFinancialBar['totalCost'][ $key ] = round($v->sum('cost'));
                    $chartFinancialBar['totalProfit'][ $key ] = round($v->sum('profit'));
                });

                $chartFinancialBarLastRecord = [
                    'labels'      => [],
                    'totalIncome' => [],
                    'totalCost'   => [],
                    'totalProfit' => []
                ];

                foreach ( $dateRange as $dateItem ) {
                    $newtmpDate = new DateTime($dateItem);
                    $newtmpDate->modify('-1Year');

                    $chartFinancialBarLastRecord['labels'][] = $newtmpDate->format('Ym');
                    $chartFinancialBarLastRecord['totalIncome'][] = 0;
                    $chartFinancialBarLastRecord['totalCost'][] = 0;
                    $chartFinancialBarLastRecord['totalProfit'][] = 0;
                }

                $last_record_bonus_list->groupBy('set_date')->map(function ( $v, $k ) use (
                    &$chartFinancialBarLastRecord
                ) {
                    $tmpDate = new DateTime($k);
                    $key = array_search($tmpDate->format('Ym'), $chartFinancialBarLastRecord['labels']);

                    $chartFinancialBarLastRecord['totalIncome'][ $key ] = round($v->sum('income'));
                    $chartFinancialBarLastRecord['totalCost'][ $key ] = round($v->sum('cost'));
                    $chartFinancialBarLastRecord['totalProfit'][ $key ] = round($v->sum('profit'));
                });

                [
                    $customerPrecentageProfit,
                    $customerProfitData,
                    $customerGroupsProfitData
                ] = $this->getCustomerAllAnalysis($bonus_list, $dateRange);
                [
                    $mediaCompaniesProfitData,
                    $mediasProfitData,
                    $saleChannelProfitData
                ] = $this->getMediaAllAnalysis($bonus_list);
                $chart_bar_max_y = collect(array_merge($chartFinancialBar['totalIncome'],
                    $chartFinancialBarLastRecord['totalIncome']))->max();
                /*取總stack*/
                $bonusCharBarStack = $bonus_list->groupBy('user_name')->map(function ( $v, $k ) {
                    return [
                        'income'      => $v->sum('income'),
                        'cost'        => $v->sum('cost'),
                        'profit'      => $v->sum('profit'),
                        'erp_user_id' => $v->max('user.erp_user_id'),
                        'sale_group_id' => $v->max('sale_group_id')
                    ];
                })->sortByDesc('erp_user_id')->toArray();
                $group_progress_list_total = $tmpGroupProgressList->groupBy('id')->map(function ( $v, $k ) {
                    return [
                        'id'                 => $v->max('id'),
                        'name'               => $v->max('name'),
                        'total_boundary'     => number_format($v->sum('boundary')),
                        'total_profit'       => number_format($v->sum('profit')),
                        'percentage_average' => round($v->sum('percentage') / $v->count())
                    ];
                })->values();

                $cacheData = [
                    $customerPrecentageProfit,
                    $customerProfitData,
                    $customerGroupsProfitData,
                    $mediaCompaniesProfitData,
                    $mediasProfitData,
                    $saleChannelProfitData,
                    $chartMoneyStatus,
                    $chartFinancialBar,
                    $bonus_list,
                    $chartFinancialBarLastRecord,
                    $chart_bar_max_y,
                    $bonusCharBarStack,
                    $group_progress_list_total
                ];

                $type = 'finance.review.mediaFilter.finalData';
                $this->cacheObj->where('type', 'financial.review')
                               ->whereIn('set_date', $dateRange)
                               ->get()
                               ->map(function (
                                   $v
                               ) use ( $conditionJson, $cacheData, $type ) {

                                   $tmp = CacheKeySub::where('key', md5($conditionJson))->first();

                                   if ( $tmp ) {
                                       $v->cacheKeySub()->attach($tmp->id);
                                   } else {
                                       $v->subput($type, $conditionJson, $cacheData);
                                   }
                               });

            } else {
                [
                    $customerPrecentageProfit,
                    $customerProfitData,
                    $customerGroupsProfitData,
                    $mediaCompaniesProfitData,
                    $mediasProfitData,
                    $saleChannelProfitData,
                    $chartMoneyStatus,
                    $chartFinancialBar,
                    $bonus_list,
                    $chartFinancialBarLastRecord,
                    $chart_bar_max_y,
                    $bonusCharBarStack,
                    $group_progress_list_total
                ] = $cacheSubObj->first()->getCacheData();
            }
            return [
                $bonus_list,
                $chartMoneyStatus,
                $chartFinancialBar,
                $chartFinancialBarLastRecord,
                $customerPrecentageProfit,
                $customerProfitData,
                $customerGroupsProfitData,
                $mediaCompaniesProfitData,
                $mediasProfitData,
                $saleChannelProfitData,
                $chart_bar_max_y,
                $bonusCharBarStack,
                $group_progress_list_total,
            ];
        }

        public function getAjaxCustomerChartData ( Request $request ,$outType = 'echo') {
            $starDate = $request->startDate;
            $endDate = $request->endDate;
            $saleGroupIds = $request->saleGroupIds;
            $userIds = $request->userIds;
            $agencyIdArrays = collect($request->agencyIdArrays)->filter()->toArray();
            $clientIdArrays = collect($request->clientIdArrays)->filter()->toArray();
            $mediaCompaniesIdArrays = collect($request->mediaCompaniesIdArrays)->filter()->toArray();
            $mediasNameArrays = collect($request->mediasNameArrays)->filter()->toArray();
            $this->cacheObj = new Cachekey('file');

            /*cache start*/
            if ( $starDate != $endDate ) {
                $dateRange = date_range($starDate, $endDate);
            }
            $dateRange[] = $endDate;

            /*check cache exists*/
            $this->cacheEachDataBase($dateRange);

            /*get other cache data*/
            /*第一層篩選 人員 日期*/
            [
                $tmpBonus,
                $tmpBonusLastRecord,
                $progress_list,
                $tmpGroupProgressList,
                $progress_list_total
            ] = $this->getUserDateSelectData($dateRange, $saleGroupIds, $userIds);

            $bonus_list = $this->getFilterData($tmpBonus->toArray(), $agencyIdArrays, $clientIdArrays,
                $mediaCompaniesIdArrays, $mediasNameArrays);

            $returnData = [
                $bonus_list,
                $tmpBonusLastRecord,
                $progress_list,
                $tmpGroupProgressList,
                $progress_list_total
            ];

            if ( $outType == 'echo' ) {
                echo json_encode($returnData);
            } else {
                return $returnData;
            }
        }
        /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
        public function getCacheDatas ( Request $request ): array {

            $starDate = $request->startDate;
            $endDate = $request->endDate;
            $saleGroupIds = $request->saleGroupIds;
            $userIds = $request->userIds;
            $agencyIdArrays = collect($request->agencyIdArrays)->filter()->toArray();
            $clientIdArrays = collect($request->clientIdArrays)->filter()->toArray();
            $mediaCompaniesIdArrays = collect($request->mediaCompaniesIdArrays)->filter()->toArray();
            $mediasNameArrays = collect($request->mediasNameArrays)->filter()->toArray();
            $this->cacheObj = new Cachekey('file');

            /*cache start*/
            if ( $starDate != $endDate ) {
                $dateRange = date_range($starDate, $endDate);
            }
            $dateRange[] = $endDate;

            /*check cache exists*/
            $this->cacheEachDataBase($dateRange);

            /*get other cache data*/
            /*第一層篩選 人員 日期*/

            [
                $tmpBonus,
                $tmpBonusLastRecord,
                $progress_list,
                $tmpGroupProgressList,
                $progress_list_total
            ] = $this->getUserDateSelectData($dateRange, $saleGroupIds, $userIds);


            /*第二層篩選 代理商'直客'媒體經銷商'媒體*/
            $condition = [
                'startDate'              => $starDate,
                'endDate'                => $endDate,
                'saleGroupIds'           => $saleGroupIds,
                'userIds'                => $userIds,
                'agencyIdArrays'         => $agencyIdArrays,
                'clientIdArrays'         => $clientIdArrays,
                'mediaCompaniesIdArrays' => $mediaCompaniesIdArrays,
                'mediasNameArrays'       => $mediasNameArrays,
            ];
            $data = [
                'tmpBonus'             => $tmpBonus,
                'tmpBonusLastRecord'   => $tmpBonusLastRecord,
                'tmpGroupProgressList' => $tmpGroupProgressList
            ];

            [
                $bonus_list,
                $chartMoneyStatus,
                $chartFinancialBar,
                $chartFinancialBarLastRecord,
                $customerPrecentageProfit,
                $customerProfitData,
                $customerGroupsProfitData,
                $mediaCompaniesProfitData,
                $mediasProfitData,
                $saleChannelProfitData,
                $chart_bar_max_y,
                $bonusCharBarStack,
                $group_progress_list_total
            ] = $this->getCustomerMediaSelectData($condition, $data);

            return [
                $progress_list,
                $tmpGroupProgressList,
                $progress_list_total,
                $bonus_list,
                $chartMoneyStatus,
                $chartFinancialBar,
                $chartFinancialBarLastRecord,
                $customerPrecentageProfit,
                $customerProfitData,
                $customerGroupsProfitData,
                $mediaCompaniesProfitData,
                $mediasProfitData,
                $saleChannelProfitData,
                $chart_bar_max_y,
                $bonusCharBarStack,
                $group_progress_list_total
            ];
        }

    }



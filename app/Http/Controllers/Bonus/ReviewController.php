<?php
    /**
     * Created by PhpStorm.
     * User: shaun
     * Date: 2019-06-20
     * Time: 10:15
     */
    
    namespace App\Http\Controllers\Bonus;
    
    use App\CustomerGroups;
    use App\Http\Controllers\FinancialController;
    use Exception;
    use Illuminate\Support\Collection;
    use Illuminate\Support\Facades\Artisan;
    use Illuminate\Support\Facades\Cache;
    use App\FinancialList;
    use App\Http\Controllers\Auth\Permission;
    use App\Http\Controllers\BaseController;
    use App\Http\Controllers\Financial\FinancialListController;
    use App\Http\Controllers\Financial\ProvideController;
    use App\Bonus;
    use App\SaleGroups;
    use App\User;
    use DateTime;
    use Gate;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Input;
    use Auth;
    
    class ReviewController extends BaseController
    {
        //
        protected $policyModel;
        
        public function __construct ()
        {
            
            parent::__construct();
            
            $this->policyModel = new FinancialList();
        }
        
        public function view ()
        {
            $loginUserId = Auth::user()->erp_user_id;
            
            $date = new DateTime();
            
            $objFin = new FinancialList();
            $agencyList = $objFin->getDataList('agency_name', 'agency_id');
            $clientList = $objFin->getDataList('client_name', 'client_id');
            $mediaCompaniesList = $objFin->getDataList('companies_name', 'companies_id');
            $medias = $objFin->getDataList('media_channel_name', 'media_channel_name');
            
            $provideObj = new ProvideController();
            list($saleGroups, $userList) = $provideObj->getDataList($loginUserId, $date);
            
            $bonusColumns =
                [
                    ['data' => 'set_date'],
                    ['data' => 'user_name'],
                    ['data' => 'sale_group_name'],
                    ['data' => 'campaign_name', 'render' => sprintf('<a href="http://%s/jsadwaysN2/campaign_view.php?id=${row.campaign_id}" target="_blank">${row.campaign_name}</a>', env('ERP_URL'))],
                    ['data' => 'media_channel_name'],
                    ['data' => 'sell_type_name'],
                    ['data' => 'currency'],
                    ['data' => 'organization'],
                    ['data' => 'income'],
                    ['data' => 'cost'],
                    ['data' => 'profit', 'render' => '<p style="${style}">${row.profit}</p>', 'parmas' => 'let style = row.organization == "hk" ? "color:red" : "" '],
                    ['data' => 'paymentStatus'],
                    ['data' => 'bonusStatus'],
                ];
            $customerGroupProfitColumns = [
                ['data' => 'name'],
                ['data' => 'receipt_times'],
                ['data' => 'profit'],
            ];
            $customerProfitColumns =
                [
                    ['data' => 'name'],
                    ['data' => 'type' ,'render' => '<span class="badge bg-${style}">${data}</span>', 'parmas' => 'let style ="red"; if(data == "直客"){ style = "blue"}'],
                    ['data' => 'receipt_times'],
                    ['data' => 'profit'],
                ];
            $mediasProfitColumns =
                [
                    ['data' => 'name'],
                    ['data' => 'sales_channel','render' => '<span class="badge bg-${style}">${data}</span>', 'parmas' => 'let style ="yellow"; if(data == "BR"){ style = "green"}else if(data == "EC"){ style = "purple"}'],
                    ['data' => 'profit'],
                ];
            $mediaCompaniesProfitColumns =
                [
                    ['data' => 'name'],
                    ['data' => 'profit'],
                ];
           
            /*ajax check debug*/
//            $dateStart = $date->format('2020-01-01');
//            $dateEnd = $date->format('2020-01-01');
//            $userIds = collect($userList)->pluck('erp_user_id')->toArray();
//            $request = new Request(['startDate' => $dateStart, 'endDate' => $dateEnd, 'saleGroupIds' => [1,2,3,4,5], 'userIds' => []]);
//            $return = $this->getAjaxData($request, 'return');
//            dd($return);
//
            $allYearProfit = $this->getAllYearProfit($userList);
            
            $chartDataBar = [
                [
                    'data' => 0,
                ],
                [
                    'data' => 0,
                ],
                [
                    'type'=> 'line','data' => 0,
                ],
            ];
            $chartData = [
                ['data' => [
                    0,
                    0,
                    0,
                    0
                ]],
                //[ 'data' => [
                //	 0,
                //	 0,
                //	 1,
                //	 1]]
            ];
            //
            $progressColumns = [
                ['data' => 'set_date', "width" => "50px"],
                ['data' => 'user_name', "width" => "50px"],
                ['data' => 'sale_group_name', "width" => "50px"],
                //['data' => 'bonus_next_percentage' , 'render' => '<span style="display: none">${data}</span><div class="progress progress-xs progress-striped active" style="${rotate}"><div class="progress-bar progress-bar-${style}" style="width: ${Math.abs(data)}%;"></div></div>','parmas' => 'let style="yellow"; let rotate=""; if(data > 90){ style = "success"}else if(data < 0){ style = "danger"; rotate = "transform: rotate(180deg)";}'],
                ['data' => 'totalProfit'],
                ['data' => 'bonus_next_percentage', 'render' => '<span class="badge bg-${style}">${data}%</span>', 'parmas' => 'let style ="yellow"; if(data > 90){ style = "green"}else if(data < 0){ style = "red"}', "width" => "50px"],
                ['data' => 'bonus_rate', 'render' => '${data}%'],
                ['data' => 'profit'],
                ['data' => 'bonus_direct']
            ];
            
            $groupsProgressColumns = [
                ['data' => 'set_date'],
                ['data' => 'name'],
                ['data' => 'profit'],
                //['data' => 'percentage' , 'render' => '<span style="display: none">${data}</span><div class="progress progress-xs progress-striped active" style="${rotate}"><div class="progress-bar progress-bar-${style}" style="width: ${Math.abs(data)}%;"></div></div>','parmas' => 'let style="yellow"; let rotate=""; if(data > 90){ style = "success"}else if(data < 0){ style = "danger"; rotate = "transform: rotate(180deg)";}'],
                ['data' => 'percentage', 'render' => '<span class="badge bg-${style}">${data}%</span>', 'parmas' => 'let style ="yellow"; if(data > 90){ style = "green"}else if(data < 0){ style = "red"}'],
            ];
            
            return view('bonus.review.view', [
                    'data' => $this->resources,
                    'chartData' => $chartData,
                    'chartDataBar' => $chartDataBar,
                    'bonusColumns' => $bonusColumns,
                    'progressColumns' => $progressColumns,
                    'groupProgressColumns' => $groupsProgressColumns,
                    'saleGroups' => $saleGroups,
                    'clientList' => $clientList,
                    'agencyList' => $agencyList,
                    'mediaCompaniesList' => $mediaCompaniesList,
                    'medias' => $medias,
                    'userList' => $userList,
                    'customerProfitColumns' => $customerProfitColumns,
                    'mediasProfitColumns' => $mediasProfitColumns,
                    'mediaCompaniesProfitColumns' => $mediaCompaniesProfitColumns,
                    'allYearProfit' => $allYearProfit,
                    'customerGroupProfitColumns' => $customerGroupProfitColumns
                ]
            );
        }
        
        public function getAjaxData (Request $request, $outType = 'echo')
        {
            $dateStart = $request->startDate;
            $dateEnd = $request->endDate;
            $saleGroupIds = $request->saleGroupIds;
            $userIds = $request->userIds;
            $agencyIdArrays = collect($request->agencyIdArrays)->filter()->toArray();
            $clientIdArrays = collect($request->clientIdArrays)->filter()->toArray();
            $mediaCompaniesIdArrays = collect($request->mediaCompaniesIdArrays)->filter()->toArray();
            $mediasNameArrays = collect($request->mediasNameArrays)->filter()->toArray();
            
            $SaleGroupsObj = new SaleGroups();
            if (!empty($userIds)) {
                $userIds = User::whereIn('id', $userIds)->get()->pluck('erp_user_id')->toArray();
            }
            
            if ($saleGroupIds && empty($userIds)) {
                $userIds = $SaleGroupsObj->all()->map(function ($v, $k) {
                    return $v->groupsUsers;
                })->flatten();
            }
            
            /*cache all user erp Id*/
            $allUserErpIds = Cache::store('memcached')->remember('allUserErpId', (4 * 360), function () {
                return User::all()->pluck('erp_user_id')->toArray();
            });
            /*cache all groupId*/
            $allGroupIds = Cache::store('memcached')->remember('allGroupId', (10 * 60), function () {
                return SaleGroups::all()->pluck('id')->toArray();
            });
            
            /*cache start*/
            if ($dateStart != $dateEnd) {
                $dateRange = date_range($dateStart, $dateEnd);
            }
            $dateRange[] = $dateEnd;
            $cacheData = collect([]);
            $dateNow = new DateTime();
            /*check cache exists*/
            $cahceKey = 'financial.review';
            
            foreach ($dateRange as $date) {
                
                $date2 = new DateTime($date);
                $dateDistance = round(($dateNow->getTimestamp() - $date2->getTimestamp()) / (3600 * 24) / 365);
                if ($dateDistance > 2) { // over two year
                    $cacheTime = 24 * 30; // 1 month
                } elseif ($dateDistance > 1) { // over one year
                    $cacheTime = 24 * 15; // 2 week
                } elseif ($dateDistance > 0.125) { // over 1.5 month
                    $cacheTime = 24; // 1 day
                } else { // close one month
                    $cacheTime = 1; // 1 hr
                };
                
                if (!Cache::store('memcached')->has($cahceKey . $date)) {
                    //
                    list($erpReturnData, $progressDatas, $groupProfitDatas) = $this->getDataFromDataBase($allUserErpIds, $allGroupIds, $date, $date);
                    
                    Cache::store('memcached')->put($cahceKey . $date, ["bonus_list" => $erpReturnData, 'progress_list' => $progressDatas, 'group_progress_list' => $groupProfitDatas], ($cacheTime * 3600));
                    
                    $cacheData[] = ["bonus_list" => $erpReturnData, 'progress_list' => $progressDatas, 'group_progress_list' => $groupProfitDatas];
                }else{
                    $cacheData[] = Cache::store('memcached')->get($cahceKey . $date);
                }
            }
            
            $group_progress_list = collect([]);
            $bonus_list = collect([]);
            $progress_list = collect([]);
            $cacheData->each(function ($v, $setDate) use (&$progress_list, &$bonus_list, &$group_progress_list) {
                //$bonus_list = array_merge($bonus_list,$v['bonus_list']->toArray());
                $bonus_list = $bonus_list->concat($v['bonus_list']);
                $progress_list = $progress_list->concat($v['progress_list']);
                $group_progress_list = $group_progress_list->concat($v['group_progress_list']);
            });
            
            $tmpBonus = collect([]);
            $tmpProgressList = collect([]);
            foreach ($dateRange as $dateItem) {
                if ($saleGroupIds) {
                    foreach ($saleGroupIds as $saleGroupId) {
                        
                        $tmpUserIds = $userIds->where('sale_groups_id', $saleGroupId)->where('set_date', $dateItem)->pluck('erp_user_id');
                        if($tmpUserIds->count() == 0){
                            $tmpdate = new DateTime($dateItem);
                            $tmpdate->modify('-1Month');
                            $tmpUserIds = $userIds->where('sale_groups_id', $saleGroupId)->where('set_date', $tmpdate->format('Y-m-01'))->pluck('erp_user_id');
                        }
                        
                        $tmpBonus = $tmpBonus->concat($bonus_list->where('set_date', substr($dateItem, 0, 7))->whereIn('erp_user_id', $tmpUserIds));
                        
                        $tmpProgressList = $tmpProgressList->concat($progress_list->where('set_date', substr($dateItem, 0, 7))->whereIn('erp_user_id', $tmpUserIds));
                    }
                } elseif ($userIds) {
                    $tmpBonus = $bonus_list->whereIn('erp_user_id', $userIds);
                    $tmpProgressList = $progress_list->whereIn('erp_user_id', $userIds);
                }
            }
            $bonus_list = $this->getFilterData($tmpBonus->toArray(), $agencyIdArrays, $clientIdArrays, $mediaCompaniesIdArrays, $mediasNameArrays);
            
            $progress_list = $tmpProgressList->values()->toArray();
            
            $chartMoneyStatus = [
                'unpaid' => round($bonus_list->where('status', '=', 0)->sum('income')),
                'paid' => round($bonus_list->where('status', '>=', 1)->sum('income'))
            ];
            $chartFinancialBar = [
                'labels' => [],
                'totalIncome' => [],
                'totalCost' => [],
                'totalProfit' => []
            ];
            foreach ($dateRange as $dateItem) {
                $newtmpDate = new DateTime($dateItem);
                $chartFinancialBar['labels'][] = $newtmpDate->format('Ym');
                $chartFinancialBar['totalIncome'][] = 0;
                $chartFinancialBar['totalCost'][] = 0;
                $chartFinancialBar['totalProfit'][] = 0;
            }
            
            
            $bonus_list->groupBy('set_date')->map(function ($v, $k) use (&$chartFinancialBar) {
                $tmpDate = new DateTime($k);
                $key = array_search($tmpDate->format('Ym'), $chartFinancialBar['labels']);
                //$chartFinancialBar['labels'][] = $tmpDate->format('Ym');
                $chartFinancialBar['totalIncome'][$key] = round($v->sum('income'));
                $chartFinancialBar['totalCost'][$key] = round($v->sum('cost'));
                $chartFinancialBar['totalProfit'][$key] = round($v->sum('profit'));
            });
            
            $bonus_list = $bonus_list->map(function ($v, $k) {
                
                $v['income'] = number_format($v['income']);
                $v['cost'] = number_format($v['cost']);
                $v['profit'] = number_format($v['profit']);
                
                return $v;
            });
            
            list($customerPrecentageProfit, $customerProfitData,$customerGroupsProfitData) = $this->getCustomerAllAnalysis($bonus_list,$dateRange);
            list($mediaCompaniesProfitData, $mediasProfitData,$saleChannelProfitData) = $this->getMediaAllAnalysis($bonus_list);
    
            $bonus_list = $bonus_list->values()->toArray();
            
            $group_progress_list = $group_progress_list->whereIn('id', $saleGroupIds)->values()->toArray();
            
            $returnData = [
                "bonus_list" => $bonus_list,
                "chart_money_status" => $chartMoneyStatus,
                "chart_financial_bar" => $chartFinancialBar,
                'progress_list' => $progress_list,
                'group_progress_list' => $group_progress_list,
                'customer_precentage_profit' => $customerPrecentageProfit,
                'customer_profit_data' => $customerProfitData,
                'medias_profit_data' => $mediasProfitData,
                'media_companies_profit_data' => $mediaCompaniesProfitData,
                'sale_channel_profit_data' => $saleChannelProfitData,
                'customer_groups_profit_data' => $customerGroupsProfitData
            ];
            
            if ($outType == 'echo') {
                echo json_encode($returnData);
            } else {
                return $returnData;
            }
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
        private function getDataFromDataBase (array $userIds, $saleGroupIds, $dateStart, $dateEnd): array
        {
            $SaleGroupsObj = new SaleGroups();
            $resignUsers = collect(session('users'))->where('user_resign_date', '!=', '0000-00-00');
            
            $erpReturnData = collect($this->getFinancialData($userIds, $dateStart, $dateEnd));
            
            /*progressDatas*/
            $progressDatas = $this->getProgressDatas($erpReturnData);
            
            //			/*補齊該月沒有 金額的user 資料*/
            foreach (date_range($dateStart, $dateEnd) as $dateRange) {
                $BonusList = Bonus::where('set_date', $dateRange)->whereIn('erp_user_id', $userIds)->get();
                $setDate = substr($dateRange, 0, 7);
                $haveValueProgressDatas = $progressDatas->where('set_date', $setDate)->pluck('erp_user_id');
                
                foreach ($BonusList->whereNotin('erp_user_id', $haveValueProgressDatas) as $item) {
                    
                    $thisResignUser = $resignUsers->whereIn('id', $item->erp_user_id)->first();
                    if ($thisResignUser['user_resign_date']) {
                        if ($thisResignUser['user_resign_date'] <= $dateRange) {
                            continue;
                        }
                    }
                    $tmpData = $this->getUserBonus($item->erp_user_id, 0, $dateRange);
                    $tmpData['totalProfit'] = number_format(0);
                    $tmpData['sale_group_name'] = $item->user->getUserGroupsName();
                    $tmpData['user_name'] = $item->user->name;
                    $tmpData['set_date'] = $setDate;
                    $tmpData['erp_user_id'] = $item->erp_user_id;
                    $progressDatas[] = $tmpData;
                }
            };
            
            ///*get group Profit */
            $groupDateStart = new DateTime($dateStart);
            $tmpGroups = [];
            /*getGroupBoundary*/
            if ($dateEnd == $groupDateStart->format('Y-m-01')) {
                $tmpGroups[] = $SaleGroupsObj->getGroupBoundary($saleGroupIds, $groupDateStart->format('Y-m-01'));
            }
            while ($dateEnd != $groupDateStart->format('Y-m-01')) {
                $tmpGroups[] = $SaleGroupsObj->getGroupBoundary($saleGroupIds, $groupDateStart->format('Y-m-01'));
                $groupDateStart->modify('+1Month');
            }
            /*calculation profit percentage*/
            $groupProfitDatas = $this->getGroupProfitDatas($tmpGroups, $erpReturnData);
            
            $erpReturnData = $erpReturnData->map(function ($v, $k) {
                $v['set_date'] = substr($v['set_date'], 0, 7);
                return $v;
            });
            
            return [$erpReturnData->toArray(), $progressDatas->toArray(), $groupProfitDatas->toArray()];
        }
        
        /**
         * @param $id
         * @param string $yearMonthDay
         * @return bool|mixed|string
         */
        private function getFinancialData (Array $erpUserIds, string $dateStart, string $dateEnd)
        {
            $financialListObj = new FinancialList();
            return $financialListObj->getFinancialData($erpUserIds, $dateStart, $dateEnd);
        }
        
        /**
         * @param $uId
         * @param $totalProfit
         * @param string $yearMonthDay
         * @return array
         */
        public function getUserBonus ($erpUserId, $totalProfit, string $yearMonthDay): array
        {
            // getUserBonus
            $bonus = new Bonus();
            $returnBonusData = $bonus->getUserBonus($erpUserId, $totalProfit, $yearMonthDay);
            
            // set output Data
            $boxData = [
                'profit' => number_format($returnBonusData['estimateBonus']),
                'bonus_rate' => isset($returnBonusData['reachLevle']['bonus_rate']) ? $returnBonusData['reachLevle']['bonus_rate'] : 0,
                'bonus_next_amount' => isset($returnBonusData['nextLevel']['bonus_next_amount']) ? round($returnBonusData['nextLevel']['bonus_next_amount']) : 0,
                'bonus_next_percentage' => isset($returnBonusData['nextLevel']['bonus_next_percentage']) ? $returnBonusData['nextLevel']['bonus_next_percentage'] : 0,
                'bonus_direct' => number_format($returnBonusData['bonusDirect'])
            ];
            
            return $boxData;
        }
        
        public function getFilterData ($financialDataArrays, $agencyIdArrays, $clientIdArrays, $mediaCompaniesIdArrays, $mediasNameArrays)
        {
           
            $financialDataArrays = collect($financialDataArrays);
            
            if (!empty($agencyIdArrays)) {
                $financialDataArrays = $financialDataArrays->whereIn('agency_id', $agencyIdArrays);
            }

            if (!empty($clientIdArrays)) {
                $financialDataArrays = $financialDataArrays->whereIn('client_id', $clientIdArrays);
            }

            if (!empty($mediaCompaniesIdArrays)) {
                $financialDataArrays = $financialDataArrays->whereIn('companies_id', $mediaCompaniesIdArrays);
            }
            
            if (!empty($mediasNameArrays)) {
                $financialDataArrays = $financialDataArrays->whereIn('media_channel_name', $mediasNameArrays);
            }
            
            return $financialDataArrays;
        }
        
        /**
         * @param Collection $bonus_list
         */
        private function getCustomerAllAnalysis (Collection $bonus_list,$dateRange)
        {
            list($customerPrecentageProfit, $customerProfitData) = [[],[]];
            /*部份資料前置時有做 number_format 這邊做還原*/
            $bonus_list = $bonus_list->map(function ($v, $k) {
                $v['profit'] = str_replace(',', '', $v['profit']);
                return $v;
            });
           
            $dateRange = collect($dateRange)->map(function($v,$k){
                return str_replace('-', '', substr($v,0,-2));
            });
            
            $customerPrecentageProfit = $this->getCustomerProfitSum($bonus_list, $dateRange);
            $customerProfitData = $this->getCustomerReceiptTimes($bonus_list, $dateRange);
            $customerGroupsProfitData = $this->getCustomerGroupsProfit($bonus_list,$dateRange);
            
            return array($customerPrecentageProfit, $customerProfitData,$customerGroupsProfitData);
        }
        
        /**
         * @param Collection $bonus_list
         * @param Collection $dateRangerArray
         * @return mixed
         */
        private function getCustomerReceiptTimes (Collection $bonus_list, Collection $dateRangerArray)
        {
            $customerProfitData = [];
            
            $receiptTimesData = collect($this->getreceiptTimes($dateRangerArray))->flatten(1);
   
                $bonus_list->filter(function ($v) {
                    return $v['agency_id'] != 0;
                })->groupBy('agency_id')->each(function ($v, $id) use (&$customerProfitData, $receiptTimesData) {
                    if($receiptTimesData->count() > 0){
                        $receiptAgencyTimes = $receiptTimesData->where('agency_id', $v->max('agency_id'))->sum('receipt_count_times');
                    }
                    $customerProfitData[] = [
                        'name' => $v->max('agency_name'),
                        'type' => '代理商',
                        'receipt_times' => $receiptAgencyTimes ?? 0,
                        'profit' => number_format($v->sum('profit')),
                    ];
                    return $v;
                });
    
                $bonus_list->filter(function ($v) {
                    return $v['agency_id'] == 0;
                })->groupBy('client_id')->each(function ($v, $id) use (&$customerProfitData, $receiptTimesData) {
                    if($receiptTimesData->count() > 0){
                        $receiptClientTimes = $receiptTimesData->where('client_id', $v->max('client_id'))->sum('receipt_count_times');
                    }
                    $customerProfitData[] = [
                        'name' => $v->max('client_name'),
                        'type' => '直客',
                        'receipt_times' => $receiptClientTimes ?? 0,
                        'profit' => number_format($v->sum('profit')),
                    ];
                    return $v;
                });
            
            
            return $customerProfitData;
        }
        
        public function getreceiptTimes ($dateRangerArray)
        {
            $erpFin = new FinancialController();
            $data = [];
            foreach ($dateRangerArray as $dateMonth) {
                if (Cache::store('memcached')->has('receiptTimes' . $dateMonth)) {
                    $data[] = Cache::store('memcached')->get('receiptTimes' . $dateMonth);
                } else {
                    $results = $erpFin->getReciptTimes($dateMonth);
                    Cache::store('memcached')->put('receiptTimes' . $dateMonth, $results, (1 * 3600));
                    $data[] = $results;
                }
            }
            return $data;
        }
        
        /**
         * @param Collection $bonus_list
         * @return mixed
         */
        private function getCustomerProfitSum (Collection $bonus_list,$dateRange)
        {
            $customerPrecentageProfit['date'] = $dateRange;
            
            $bonus_list->groupBy('set_date')->map(function ($v, $date) use (&$customerPrecentageProfit) {
                $key = $customerPrecentageProfit['date']->search(str_replace('-', '', $date));
                $customerPrecentageProfit['agency_profit'][$key] = $v->where(
                    'agency_id', '!=', 0
                )->sum('profit');
                
                $customerPrecentageProfit['client_profit'][$key] = $v->where(
                    'agency_id', '=', 0
                )->sum('profit');
            });
            foreach ($customerPrecentageProfit['date'] as $key => $item){
                if(!isset($customerPrecentageProfit['agency_profit'][$key])){
                    $customerPrecentageProfit['agency_profit'][$key] = 0;
                    $customerPrecentageProfit['client_profit'][$key] = 0;
                }
            }
            
            if(empty($customerPrecentageProfit['agency_profit'])){
                $customerPrecentageProfit['agency_profit'][] = 0;
                $customerPrecentageProfit['client_profit'][] = 0;
            }
            $customerPrecentageProfit['agency_profit'] = collect($customerPrecentageProfit['agency_profit'])->sortKeys();
            $customerPrecentageProfit['client_profit'] = collect($customerPrecentageProfit['client_profit'])->sortKeys();
            
            return $customerPrecentageProfit;
        }
        
        /**
         * @param $erpReturnData
         * @return array
         */
        public function calculationTotal ($erpReturnData): array
        {
            $totalIncome = 0;
            $totalCost = 0;
            $totalProfit = 0;
            if (empty($erpReturnData)) {
                $erpReturnData = [];
            } else {
                $erpReturnData = collect($erpReturnData);
                $totalIncome = $erpReturnData->sum('income');
                $totalCost = $erpReturnData->sum('cost');
                $totalProfit = $erpReturnData->sum('profit');
                
                $erpReturnData = $erpReturnData->map(function ($items) {
                    $items = gettype($items) == 'object' ? get_object_vars($items) : $items;
                    
                    if ($items['income'] != 0 || $items['cost'] != 0) {
                        
                        return $items;
                    }
                })->filter()->values()->toArray();
            }
            return array($totalIncome, $totalCost, $totalProfit, $erpReturnData);
        }
    
        /**
         * @param Collection $bonus_list
         * @return array
         */
        private function getMediaAllAnalysis (Collection $bonus_list): array
        {
            list($mediaCompaniesProfitData, $mediasProfitData) = [[],[]];
            /*部份資料前置時有做 number_format 這邊做還原*/
            $bonus_list = $bonus_list->map(function ($v, $k) {
                $v['profit'] = str_replace(',', '', $v['profit']);
                return $v;
            });
            
            $saleChannelProfitData = [
                'AP' => 0,
                'BR' => 0,
                'EC' => 0,
            ];
            $bonus_list->groupBy('sales_channel')->each(function($v,$k) use(&$saleChannelProfitData){
                $saleChannelProfitData[$v->max('sales_channel')] = $v->sum('profit');
            });
            
            $mediaCompaniesProfitData = $bonus_list->groupBy('companies_id')->map(function ($v, $k) {
                $data = [
                    'name' => $v->max('companies_name'),
                    'profit' => number_format($v->sum('profit')),
                ];
                return $data;
            })->values();
        
            $mediasProfitData = $bonus_list->groupBy('media_channel_name')->map(function ($v, $k) {
                $data = [
                    'name' => $v->max('media_channel_name'),
                    'sales_channel' => $v->max('sales_channel'),
                    'profit' => number_format($v->sum('profit')),
                ];
                return $data;
            })->values();
            
            return array($mediaCompaniesProfitData, $mediasProfitData,$saleChannelProfitData);
        }
    
        /**
         * @param Collection $erpReturnData
         * @return Collection
         */
        private function getProgressDatas (Collection $erpReturnData): Collection
        {
            $progressDatas = collect([]);
            $erpReturnData->groupBy(['set_date', 'erp_user_id'])->map(function ($items, $setDate) use (&$progressDatas) {
            
                $items = $items->map(function ($v, $erpUserId) use ($setDate) {
                    $tmpData = $this->getUserBonus($erpUserId, $v->sum('profit'), $setDate);
                    $tmpData['totalProfit'] = number_format($v->sum('profit'));
                    $tmpData['sale_group_name'] = $v->max('sale_group_name');
                    $tmpData['user_name'] = $v->max('user_name');
                    $tmpData['set_date'] = substr($setDate, 0, 7);
                    $tmpData['erp_user_id'] = $erpUserId;
                    return $tmpData;
                })->values();
                $progressDatas = $progressDatas->concat($items);
            });
            return $progressDatas;
        }
    
        /**
         * @param array $tmpGroups
         * @param Collection $erpReturnData
         * @return Collection
         */
        private function getGroupProfitDatas (array $tmpGroups, Collection $erpReturnData): Collection
        {
            $groupProfitDatas = collect([]);
        
            collect($tmpGroups)->map(function ($items, $k) use (&$groupProfitDatas, $erpReturnData) {
                $items = collect($items)->map(function ($item, $k) use ($erpReturnData) {
                
                    $item['profit'] = round($erpReturnData->where('sale_group_id', $item['id'])->where('set_date', $item['set_date'])->sum('profit'));
                    $item['percentage'] = ($item['profit'] == 0 || $item['boundary'] == 0) ? 0 : round($item['profit'] / $item['boundary'] * 100);
                    $item['profit'] = number_format($item['profit']);
                    $item['set_date'] = substr($item['set_date'], 0, 7);
                    return $item;
                });
                $groupProfitDatas = $groupProfitDatas->concat($items);
            });
            return $groupProfitDatas;
        }
    
        /**
         * @param DateTime $date
         * @param $userList
         * @return mixed
         * @throws \Psr\SimpleCache\InvalidArgumentException
         */
        private function getAllYearProfit ()
        {
            $allYearProfit = [
                'data' => [],
                'stack' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            ];
    
    
    
            $saleGroupIds = SaleGroups::all()->pluck('id')->toArray();
            $date = new DateTime();
            if(!Cache::store('memcached')->has('yearProfit')){
                /*取所有年度毛利統計資料*/
                $dateStart = '2018-01-01';
                $dateEnd = $date->format('Y-12-01');
                $dateRange = date_range($dateStart, $dateEnd);
                $dateRange[] = $dateEnd;
                
                $request = new Request(['startDate' => $dateStart, 'endDate' => $dateEnd, 'saleGroupIds' => $saleGroupIds, 'userIds' => []]);
                $return = $this->getAjaxData($request, 'return')['chart_financial_bar']['totalProfit'];
                
                collect($return)->chunk(12)->each(function($v,$k) use(&$allYearProfit){
                    $allYearProfit['data'][2018+$k] = $v->values()->toArray();
                });
                /*最後進兩個月資料不存cache*/
                array_pop($return);
                array_pop($return);
    
                Cache::store('memcached')->forever('yearProfit', $return);
            }else{
                $cacheData = Cache::store('memcached')->get('yearProfit');
                $thisMonth = $date->format('Y-m-01');
                $lastMonth = $date->modify('-1Month')->format('Y-m-01');
                $dateRange = date_range($lastMonth, $thisMonth);
                $request = new Request(['startDate' => $lastMonth, 'endDate' => $thisMonth, 'saleGroupIds' => $saleGroupIds, 'userIds' => []]);
                $return = $this->getAjaxData($request, 'return')['chart_financial_bar']['totalProfit'];
                
                collect(array_merge($cacheData,$return))->chunk(12)->each(function($v,$k) use(&$allYearProfit){
                    $allYearProfit['data'][2018+$k] = $v->values()->toArray();
                });
            }

            return $allYearProfit;
        }
    
        private function getCustomerGroupsProfit (Collection $bonus_list,$dateRange)
        {
            $customerGroup = new CustomerGroups();
            $customerGroup = collect($customerGroup->getCustomerGroupDatas());
            $receiptTimesData = collect($this->getreceiptTimes($dateRange))->flatten(1);
            
            $customerGroupsProfit = $customerGroup->map(function($v,$k) use($bonus_list,$receiptTimesData){
                
                $profit = $bonus_list->whereIn('agency_id',$v['customer']['agency'])->concat($bonus_list->whereIn('client_id',$v['customer']['client']))->sum('profit');
                
                $receiptTimes = $receiptTimesData->whereIn('agency_id',$v['customer']['agency'])->concat($receiptTimesData->whereIn('client_id',$v['customer']['client']))->sum('receipt_count_times');
                
                $newdata = [
                  'name' => $v['name'],
                  'profit' => number_format($profit),
                'receipt_times' => $receiptTimes,
                ];
                return $newdata;
            });
            
            return $customerGroupsProfit;
        }
    }
	
	

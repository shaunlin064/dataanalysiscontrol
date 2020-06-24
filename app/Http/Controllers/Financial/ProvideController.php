<?php


    namespace App\Http\Controllers\Financial;

    ini_set('max_execution_time', 600);
    ini_set('memory_limit', '1024M');

    use App\Bonus;
    use App\FinancialList;
    use App\Http\Controllers\BaseController;
    use App\Http\Controllers\FinancialController;
    use App\Provide;
    use App\SaleGroups;
    use App\SaleGroupsReach;
    use App\SaleGroupsUsers;
    use App\User;
    use DateTime;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\Input;
    use Route;

    class ProvideController extends BaseController {
        //
        protected $cacheKeyProvide = 'financial.provide';
        protected $cacheKeyFinancial = 'financial.list';
        /*判斷獎金發放資料要入本月還是次月 日期*/
        protected $saveDateLine = 6;
        protected $policyModel;

        public function __construct () {

            parent::__construct();

            $this->policyModel = new Provide();
        }

        public function list () {

            $this->authorize('viewSetting', $this->policyModel);

            /*check cache exists*/
            $cacheData = collect([]);
            $dataDate  = now();
            if ( now()->format('d') < 16 ) {
                $dataDate->modify('-1Month');
            }

            if ( !Cache::store('memcached')->has($this->cacheKeyProvide) ) {
                /*過濾後勤單位*/
                $erpUSerId = Bonus::all()->filter(
                    function ( $v ) {
                        if ( $v->saleGroups->count() > 0 ) {
                            return $v->saleGroups->max('sale_groups_id') != 4;
                        }
                    }
                )->pluck('erp_user_id')->unique()->values();
                $bonuslist = FinancialList::where('status', 1)
                                          ->where('set_date', '<', $dataDate->format('Y-m-01'))
                                          ->where('profit', '<>', 0)
                                          ->whereIn('erp_user_id', $erpUSerId)
                                          ->get();

                $bonuslist = $bonuslist->map(
                    function ( $v, $k ) {
                        //				$v['receipt_date'] = empty($v->receipt->created_at) ? '' : $v->receipt->created_at->format('Y-m-d');
                        $v['sale_group_name']  = $v->saleGroups->saleGroups->name ?? '';
                        $v['user_name']        = ucfirst($v->user->name);
                        $v['rate']             = $v->bonus->bonusReach->reach_rate ?? 0;
                        $v['profit']           = $this->exchangeMoney($v);
                        $v['provide_money']    = round($v['profit'] * $v['rate'] / 100);
                        $v['set_date']         = substr($v['set_date'], 0, 7);
                        $v['user_resign_date'] = session('users')[ $v->erp_user_id ]['user_resign_date'];
                        return $v;
                    }
                )->values();

                $saleGroupsReach = SaleGroupsReach::where('status', 0)->get();

                $saleGroupsReach = $saleGroupsReach->map(
                    function ( $v, $k ) {
                        $v->user_name  = ucfirst($v->saleUser->user->name);
                        $v->group_name = $v->saleGroups->name;
                        $v->set_date   = substr($v->set_date, 0, 7);
                        return $v;
                    }
                )->toArray();

                Cache::store('memcached')->put(
                        $this->cacheKeyProvide, [
                        "bonuslist"       => $bonuslist,
                        'saleGroupsReach' => $saleGroupsReach
                    ], ( 8 * 3600 )
                    );
            }
            $cacheData = Cache::store('memcached')->get($this->cacheKeyProvide);

            $bonuslist       = $cacheData['bonuslist'];
            $saleGroupsReach = $cacheData['saleGroupsReach'];

            $saleGroupsTableColumns = [
                [ 'data' => 'id' ],
                [ 'data' => 'set_date' ],
                [ 'data' => 'user_name' ],
                [ 'data' => 'group_name' ],
                [ 'data' => 'groups_profit' ],
                [ 'data' => 'rate' ],
                [
                    'data'   => 'provide_money',
                    'render' => '<div data-money="${data}">${data}</div>'
                ]
            ];

            $bonuslistColumns = [
                [ 'data' => 'id' ],
                //['data' => 'receipt_date'],
                [ 'data' => 'set_date' ],
                [
                    'data'   => 'user_name',
                    'render' => '<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="離職日${row.user_resign_date}"><a>${data}</a></span>'
                ],
                [ 'data' => 'sale_group_name' ],
                [
                    'data'   => 'campaign_name',
                    'render' => sprintf(
                        '<a href="http://%s/jsadwaysN2/campaign_view.php?id=${row.campaign_id}" target="_blank">${row.campaign_name}</a>',
                        env('ERP_URL')
                    )
                ],
                [ 'data' => 'media_channel_name' ],
                [ 'data' => 'sell_type_name' ],
                [ 'data' => 'profit' ],
                [ 'data' => 'rate' ],
                [
                    'data'   => 'provide_money',
                    'render' => '<div data-money="${data}">${data}</div>'
                ],
            ];

            //		$autoSelectIds = $this->getProvideBalanceSelectedId($bonuslist);
            //		$total_mondey = $bonuslist->whereIn('id',$autoSelectIds)->sum('provide_money');

            $allUserName = collect(array_merge($bonuslist->toArray(), $saleGroupsReach))->pluck(
                    'erp_user_id', 'user_name'
                )->map(
                    function ( $v, $k ) {
                        if ( empty($v) ) {
                            $v = User::where('name', $k)->first()->erp_user_id;
                        }
                        return $v;
                    }
                );

            return view(
                'financial.provide.list', [
                                            'data'                   => $this->resources,
                                            'saleGroupsReach'        => $saleGroupsReach,
                                            'saleGroupsTableColumns' => $saleGroupsTableColumns,
                                            'bonuslistColumns'       => $bonuslistColumns,
                                            'bonuslist'              => $bonuslist,
                                            'allUserName'            => $allUserName,
                                            'autoSelectIds'          => [],
                                            'total_mondey'           => 0,
                                        ]
            );

            //columns : [
            //                {data: "groups_users", render: '<p class="hidden">${data}</p><input id="checkbox${row.erp_user_id}" class="groupsUsers" type="checkbox" value=${row.erp_user_id} ${checkt}>',parmas:'let checkt = data == 1 ? "checked" : "" '},
            //                {data: "groups_is_convener", render: '<p class="hidden">${data}</p><input class="is_convener" type="checkbox" value=${row.erp_user_id} ${checkt}>',parmas:'let checkt = data == 1 ? "checked" : "" '},
            //                {data: "name", render: '<label class="point" for=checkbox${row.erp_user_id}>${data}</label>'},
            //                {data: "boundary", render: '<label class="point" data-boundary_id=${row.erp_user_id} data-boundary=${data} for=checkbox${row.erp_user_id}>${data}</label>'},
            //                {data: "sale_groups_name", render: '<label class="point" for=checkbox${row.erp_user_id}>${data}</label>'}
            //            ],


        }

        public function view () {
            $date      = new DateTime(date('Ym01'));
            $erpUserId = Auth::user()->erp_user_id;
            //
//            $provideStart = '2020-05-01';
//            $provideEnd = '2020-05-01';
//            $saleGroupIds = [1, 2, 3, 4,5,6,7,8];
//            $userIds = [];
//            $request = new Request(['startDate' => $provideStart, 'endDate' => $provideEnd, 'saleGroupIds' => $saleGroupIds, 'userIds' => $userIds]);
//            $datas = $this->getAjaxProvideData($request, 'return');
//            dd($datas);

            [
                $saleGroups,
                $userList
            ]
                = $this->getDataList($erpUserId, $date);

            //$provideStart = new DateTime();
            //$provideEnd = new DateTime();
            //$userIds = collect($userList)->pluck('erp_user_id')->toArray();
            //$saleGroupsReach = $this->getSaleGroupProvide($provideStart, $provideEnd, $userIds);
            //$provideBonus = $this->getUserBounsProvide($provideStart, $provideEnd, $userIds);

            $provideBonusColumns = [
                [ 'data' => 'provide_set_date' ],
                [ 'data' => 'set_date' ],
                [ 'data' => 'user_name' ],
                [ 'data' => 'sale_group_name' ],
                [
                    'data'   => 'campaign_name',
                    'render' => sprintf(
                        '<a href="http://%s/jsadwaysN2/campaign_view.php?id=${row.campaign_id}" target="_blank">${row.campaign_name}</a>',
                        env('ERP_URL')
                    )
                ],
                [ 'data' => 'media_channel_name' ],
                [ 'data' => 'sell_type_name' ],
                [ 'data' => 'profit' ],
                [ 'data' => 'rate' ],
                [ 'data' => 'provide_money' ],
            ];

            $saleGroupsReachColumns = [
                [ 'data' => 'provide_set_date' ],
                [ 'data' => 'set_date' ],
                [ 'data' => 'user_name' ],
                [ 'data' => 'sale_group_name' ],
                [ 'data' => 'groups_profit' ],
                [ 'data' => 'rate' ],
                [ 'data' => 'provide_money' ],
            ];

            return view(
                'financial.provide.view', [
                                            'data'                   => $this->resources,
                                            'provideBonusColumns'    => $provideBonusColumns,
                                            'provideBonus'           => [],
                                            'saleGroupsReachColumns' => $saleGroupsReachColumns,
                                            'saleGroupsReach'        => [],
                                            'saleGroups'             => $saleGroups,
                                            'userList'               => $userList
                                        ]
            );
        }

        public function getAllSelectId () {
            $row = FinancialList::where([ 'status' => '0' ])->select('id')->pluck('id');

            return $row;
        }

        public function ajaxCalculatFinancialBonus () {

            $selectFincialIds = Input::post('select_financial_ids') ?? [];
            $selectFincialIds = explode(',', $selectFincialIds);

            $financialData = FinancialList::join('users', 'financial_lists.erp_user_id', '=', 'users.erp_user_id')
                                          ->leftJoin(
                                              'bonus', function ( $join ) {
                                              $join->on('financial_lists.erp_user_id', '=', 'bonus.erp_user_id')->on(
                                                      'financial_lists.set_date', '=', 'bonus.set_date'
                                                  );
                                          }
                                          )
                                          ->leftJoin(
                                              'bonus_reach', function ( $join ) {
                                              $join->on('bonus.id', '=', 'bonus_reach.bonus_id');
                                          }
                                          )
                                          ->leftJoin(
                                              'financial_provides', function ( $join ) {
                                              $join->on(
                                                  'financial_lists.id', '=', 'financial_provides.financial_lists_id'
                                              );
                                          }
                                          )
                                          ->select(
                                              'financial_provides.created_at as provide_date', 'bonus.id as bonus_id',
                                              'bonus_reach.reach_rate', 'users.name', 'financial_lists.*'
                                          )
                                          ->whereIn('financial_lists.id', $selectFincialIds)
                                          ->get();

            $financialData = $financialData->map(
                function ( $v, $k ) {
                    if ( !empty($v['reach_rate']) && $v['profit'] > 0 ) {
                        $exchangeProfitMoney = $this->exchangeMoney($v);

                        $bonusReach = isset($v->bonus) ? $v->bonus->bonusReach : [];
                        $reachRate  = $bonusReach->reach_rate ?? 0;
                        return $exchangeProfitMoney * $reachRate / 100;
                    }
                }
            );


            echo round($financialData->sum());
        }

        public function post ( Request $request ) {
            $this->authorize('create', $this->policyModel);

            $selectSaleGroupsReachIds = explode(',', $request->provide_sale_groups_bonus);

            $this->setSaleGroupsReachProvide($selectSaleGroupsReachIds);

            $selectFincialIds = $request->provide_bonus;
            $selectFincialIds = $selectFincialIds != null ? explode(',', $selectFincialIds) : [];
            $this->resetFinancialStatus();
            $this->save($selectFincialIds);

            $this->cacheRelease();

            $message['status_string'] = 'success';
            $message['message']       = '更新成功';


            return view(
                'handle', [
                            'message'   => $message,
                            'data'      => $this->resources,
                            'returnUrl' => Route('financial.provide.list')
                        ]
            );
        }

        public function getAjaxProvideData ( Request $request, $outType = 'echo' ) {
            $provideStart = new DateTime($request->startDate);
            $provideEnd   = new DateTime($request->endDate);
            $saleGroupIds = $request->saleGroupIds;
            $userIds      = $request->userIds;
            $dateRange = date_range($provideStart->format('Y-m-01'), $provideEnd->format('Y-m-01'));

            if ( !empty($userIds) ) {
                $userIds = User::whereIn('id', $userIds)->get()->pluck('erp_user_id')->toArray();
            }

            if ( $saleGroupIds && $userIds == null ) {
                $userIds = SaleGroups::with('groupsUsers')->whereIn('id', $saleGroupIds)->get()->map(
                        function ( $v, $k ) use($dateRange) {
                            return $v->groupsUsers->whereIn('set_date',$dateRange)->pluck('erp_user_id');
                        }
                    )->flatten()->unique()->values();
            }
            $saleGroupIds = User::whereIn('erp_user_id', $userIds)->get()->map(
                    function ( $v, $k ) use($dateRange) {
                        return $v->userGroups->whereIn('set_date',$dateRange)->pluck('sale_groups_id');
                    }
                )->flatten()->unique()->values();

            /*cache start*/
            $cacheData   = collect([]);
            $dateNow     = new DateTime();
            /*check cache exists*/
            /*cache all user erp Id*/

            $allUserErpIds = Cache::store('memcached')->remember(
                'allUserErpId', ( 4 * 360 ), function () {
                return User::all()->pluck('erp_user_id')->toArray();
            }
            );
            foreach ( $dateRange as $date ) {
                $dateTimeObj = new DateTime($date);

                if ( !Cache::store('memcached')->has($this->cacheKeyFinancial . $date) ) {
                    $saleGroupRowData = $this->getSaleGroupProvide($dateTimeObj, $dateTimeObj, $allUserErpIds, []);
                    $bonusRowData     = $this->getUserBounsProvide($dateTimeObj, $dateTimeObj, $allUserErpIds, [])
                                             ->where('profit', '<>', 0);

                    /*TODO::優化快取暫存時間判斷*/
                    $date2        = $dateTimeObj;
                    $cacheTime    = 1;//hr
                    $dateDistance = ( $dateNow->getTimestamp() - $date2->getTimestamp() ) / ( 60 * 60 * 24 ) / 365;


                    if ( $dateDistance > 0.1 ) { // over 1 month
                        Cache::store('memcached')->forever(
                                $this->cacheKeyFinancial . $date, [
                                                                    'saleGroupRowData' => $saleGroupRowData,
                                                                    'bonusRowData'     => $bonusRowData
                                                                ]
                            );
                    } else { // close one month
                        Cache::store('memcached')->put(
                                $this->cacheKeyFinancial . $date, [
                                                                    'saleGroupRowData' => $saleGroupRowData,
                                                                    'bonusRowData'     => $bonusRowData
                                                                ], ( $cacheTime * 3600 )
                            );
                    };
                }
                $cacheData[] = Cache::store('memcached')->get($this->cacheKeyFinancial . $date);

            }
            $saleGroupRowData = collect([]);
            $bonusRowData     = collect([]);

            $cacheData->map(
                function ( $v, $setDate ) use ( &$saleGroupRowData, &$bonusRowData ) {

                    $saleGroupRowData = $saleGroupRowData->concat($v['saleGroupRowData']);
                    $bonusRowData     = $bonusRowData->concat($v['bonusRowData']);

                }
            );
            /*get provide Bar TrimData*/
            $allName = Bonus::all()->pluck('erp_user_id')->unique()->values();

            $provideCharBarStack = $bonusRowData->groupBy('provide_set_date')->map(
                function ( $v, $k ) {
                    $results = $v->groupBy('user_name')->map(
                        function ( $v, $k ) {
                            return [
                                'provide_money' => $v->sum('provide_money'),
                                'erp_user_id'   => $v->max('user.erp_user_id')
                            ];
                        }
                    );
                    return $results;
                }
            )->map(
                function ( $items, $k ) use ( $allName ) {

                    $allName->each(
                        function ( $v, $k ) use ( &$items ) {
                            if ( count($items->whereIn('erp_user_id', $v)) == 0 ) {
                                $name           = ucfirst(User::where('erp_user_id', $v)->first()->name);
                                $items[ $name ] = [
                                    'provide_money' => 0,
                                    'erp_user_id'   => $v
                                ];
                            }
                        }
                    );
                    return $items->sortByDesc('erp_user_id');
                }
            )->toArray();

            $saleGroupRowData->each(
                function ( $v, $k ) use ( &$provideCharBarStack ) {
                    if ( !isset($provideCharBarStack[ $v->provide_set_date ]) ) {
                        $provideCharBarStack[ $v->provide_set_date ] = [];
                    }
                    if ( !isset($provideCharBarStack[ $v->provide_set_date ][ $v->user_name ]) ) {
                        $provideCharBarStack[ $v->provide_set_date ][ $v->user_name ] = [];
                    }
                    if ( !isset($provideCharBarStack[ $v->provide_set_date ][ $v->user_name ]['provide_money']) ) {
                        $provideCharBarStack[ $v->provide_set_date ][ $v->user_name ]['provide_money'] = 0;
                    }
                    $provideCharBarStack[ $v->provide_set_date ][ $v->user_name ]['provide_money'] += $v->provide_money;

                }
            );

            $provideCharBarStack = collect($provideCharBarStack)->map(
                function ( $v, $k ) use ( $userIds ) {
                    return collect($v)->whereIn('erp_user_id', $userIds);
                }
            );
            $provide_groups_list = $saleGroupRowData->whereIn('erp_user_id', $userIds);
            $provide_bonus_list = $bonusRowData->whereIn('erp_user_id', $userIds);

            if($saleGroupIds){
                $provide_groups_list = $provide_groups_list->whereIn('sale_groups_id', $saleGroupIds);
                $provide_bonus_list = $provide_bonus_list->whereIn('sale_groups_id', $saleGroupIds);
            }

            $returnData = [
                "provide_groups_list"    => $provide_groups_list->values()->toArray(),
                "provide_bonus_list"     => $provide_bonus_list->values()->toArray(),
                "provide_char_bar_stack" => $provideCharBarStack->toArray(),
            ];

            if ( $outType == 'echo' ) {
                echo json_encode($returnData);
            } else {
                return $returnData;
            }

        }

        /**
         * @param bool $isConvener
         * @param $saleGroupsUsers
         * @param $erpUserId
         * @param DateTime $date
         * @return array
         */
        public function getDataList ( $erpUserId, DateTime $date ): array {
            /*permission check select*/
            $isAdmin            = User::where('erp_user_id', $erpUserId)->first()->isAdmin();
            $isBusinessDirector = User::where('erp_user_id', $erpUserId)->first()->isBusinessDirector();
            $dateStr            = $date->format('Y-m-01');
            /*convener check*/
            $saleGroupsUsers = SaleGroupsUsers::where(
                [
                    'erp_user_id' => $erpUserId,
                    'set_date'    => $dateStr
                ]
            )->first();
            $isConvener      = $saleGroupsUsers->is_convener ?? false;

            /* 依照權限不同 取的 user list 資料差異
                    admin 全取
                    convener 取該團隊
                    user 取自己
                    */
            $saleGroupsIds = [];
            $saleGroups    = [];
            $userList      = [];
            $userIds       = [];

            if ( $isAdmin || $isBusinessDirector ) {
                $saleGroups = SaleGroups::all();
                $userList   = Bonus::with('user')->groupBy('erp_user_id')->orderBy('erp_user_id')->get()->map(
                        function ( $v, $k ) {
                            $newUser       = $v->user;
                            $newUser->name = ucfirst($v->user->name);
                            return $newUser;
                        }
                    );
            } else {
                if ( $isConvener ) {
                    $saleGroups = [ $saleGroupsUsers->saleGroups ];

                    $userGroupIds = $saleGroupsUsers->getSameGroupsUser($erpUserId, $dateStr)->pluck('user')->pluck(
                            'erp_user_id'
                        )->toArray();

                    $userList = User::whereIn('erp_user_id', $userGroupIds)->get();
                } else {
                    $saleGroups = [];

                    $userList = User::where('erp_user_id', $erpUserId)->get();
                }
            }

            return [
                $saleGroups,
                $userList->toArray()
            ];
        }

        /**
         * @param array $selectFincialIds
         */
        private function save ( array $selectFincialIds ): void {
            $createdTime = new DateTime();
            if ( $createdTime->format('d') >= $this->saveDateLine ) {
                $createdTime->modify('+1Month');
            }

            $financialList = FinancialList::whereIn('id', $selectFincialIds)->get();
            //add && update
            $financialList->map(
                function ( $v ) use ( $createdTime ) {
                    //save financialList
                    $v->status = 2;
                    $v->save();
                    $v->refresh();

                    //calculat exchangeProfit
                    $exchangeProfitMoney = $this->exchangeMoney($v);

                    $financial_lists_id = $v->id;
                    $bonusReach         = isset($v->bonus) ? $v->bonus->bonusReach : [];
                    $bonusId            = $bonusReach->bonus_id ?? 0;
                    $reachRate          = $bonusReach->reach_rate ?? 0;
                    $provideMoney       = $exchangeProfitMoney * $reachRate / 100;


                    $provide = Provide::where('financial_lists_id', $financial_lists_id)->first();

                    $provideData = [
                        'bonus_id'           => $bonusId,
                        'financial_lists_id' => $financial_lists_id,
                        'provide_money'      => $provideMoney,
                        'created_at'         => $createdTime->format('Y-m-01'),
                    ];

                    if ( isset($provide) ) {
                        //update
                        foreach ( $provideData as $key => $item ) {
                            $provide->$key = $item;
                        }
                        $provide->save();
                    } else {
                        //new
                        Provide::create($provideData);
                    }

                }
            );
        }

        /**
         * @param $v
         * @return FinancialController
         */
        private function exchangeMoney ( $v ) {
            $fincialList = new FinancialList();

            return $fincialList->exchangeMoney($v)->profit;
        }

        private function resetFinancialStatus (): void {
            $provideFid = Provide::all()->pluck('financial_lists_id');
            FinancialList::WhereIn('id', $provideFid)->update([ 'status' => 2 ]);
        }

        /**
         * @param DateTime $provideStart
         * @param DateTime $provideEnd
         * @param $userIds
         * @return SaleGroupsReach[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
         */
        private function getSaleGroupProvide ( DateTime $provideStart, DateTime $provideEnd, $userIds = null,
                                               $saleGroupIds = null ) {

            if ( $saleGroupIds && $userIds == null ) {
                $userIds = SaleGroups::with('groupsUsers')->whereIn('id', $saleGroupIds)->get()->map(
                        function ( $v, $k ) {
                            return $v->groupsUsers->pluck('erp_user_id');
                        }
                    )->flatten();
            }

            /* sale Groups Bonus*/
            $saleGroupsReach = SaleGroupsReach::with('saleGroups', 'saleUser')->where('status', 1)->whereBetween(
                    'updated_at', [
                    $provideStart->format('Y-m-01'),
                    $provideEnd->format('Y-m-31')
                ]
                )->get();

            $saleGroupsReach = $saleGroupsReach->whereIn('saleUser.erp_user_id', $userIds);
            $saleGroupsReach = $saleGroupsReach->map(
                function ( $v, $k ) {
                    $v['erp_user_id']      = $v->saleUser->erp_user_id;
                    $v['provide_set_date'] = $v->updated_at->format('Y-m');
                    $v['user_name']        = ucfirst($v->saleUser->user->name);
                    $v['sale_group_name']  = $v->saleGroups->name;
                    $v['set_date']         = substr($v['set_date'], 0, 7);
                    return $v;
                }
            )->values();
            return $saleGroupsReach;
        }

        /**
         * @param DateTime $provideStart
         * @param DateTime $provideEnd
         * @param $userIds
         * @return FinancialList[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
         */
        private function getUserBounsProvide ( DateTime $provideStart, DateTime $provideEnd, $userIds = null,
                                               $saleGroupIds = null ) {

            if ( $saleGroupIds && $userIds == null ) {
                $userIds = SaleGroups::with('groupsUsers')->whereIn('id', $saleGroupIds)->get()->map(
                        function ( $v, $k ) {
                            return $v->groupsUsers->pluck('erp_user_id');
                        }
                    )->flatten();
            }

            // financial bonus list
            $provideBonus = FinancialList::with(
                [
                    'provide',
                    'user'
                ]
            )->get();
            $provideBonus = $provideBonus->whereBetween(
                'provide.created_at', [
                                        $provideStart->format('Y-m-01'),
                                        $provideEnd->format('Y-m-31')
                                    ]
            )->whereIn('erp_user_id', $userIds)->values();

            $provideBonus = $provideBonus->map(
                function ( $v, $k ) {
                    $v['sale_group_name']  = isset($v->saleGroups) ? $v->saleGroups->saleGroups->name : '';
                    $v['sale_groups_id'] =  isset($v->saleGroups) ? $v->saleGroups->sale_groups_id : '';
                    $v['user_name']        = ucfirst($v->user->name);
                    $v['provide_set_date'] = $v->provide->created_at->format('Y-m');
                    $v['provide_money']    = $v->provide->provide_money;
                    $v['rate']             = $v->provide->bonusReach->reach_rate ?? 0;
                    $v['set_date']         = substr($v['set_date'], 0, 7);
                    return $v;
                }
            )->values();
            return $provideBonus;
        }

        /**
         * @param array $selectSaleGroupsReachIds
         */
        private function setSaleGroupsReachProvide ( array $selectSaleGroupsReachIds ): void {
            $saleGroupReach = new SaleGroupsReach();
            $createdTime    = new DateTime();
            if ( $createdTime->format('d') >= $this->saveDateLine ) {
                $createdTime->modify('+1Month');
            }

            $saleGroupReach->whereIn('id', $selectSaleGroupsReachIds)->update(
                    [
                        'status'     => 1,
                        'updated_at' => $createdTime->format('Y-m-01')
                    ]
                );
        }

        private function getProvideBalanceSelectedId ( $dataList ) {
            $dataList  = $dataList->groupBy('erp_user_id');
            $selectIds = $dataList->map(
                function ( $v, $erpUserId ) {
                    $isAlive = session('users')[ $erpUserId ]['user_resign_date'] == '0000-00-00';
                    if ( $isAlive && $v->sum('provide_money') >= 0 ) {
                        return $v->pluck('id');
                    }
                }
            )->filter()->flatten();

            return $selectIds;
        }

        private function cacheRelease (): void {
            $date = new DateTime();
            Cache::store('memcached')->forget($this->cacheKeyFinancial . $date->format('Y-m-01'));
            Cache::store('memcached')->forget($this->cacheKeyProvide);
            $date->modify('-1Month');
            Cache::store('memcached')->forget($this->cacheKeyFinancial . $date->format('Y-m-01'));
            Cache::store('memcached')->forget($this->cacheKeyProvide);
        }
    }


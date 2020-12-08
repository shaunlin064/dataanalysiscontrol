<?php
	
	
	namespace App\Http\Controllers\Financial;
	
	use App\Bonus;
	use App\Cachekey;
	use App\FinancialList;
	use App\Http\Controllers\BaseController;
	use App\Http\Controllers\FinancialController;
	use App\Provide;
	use App\SaleGroups;
	use App\SaleGroupsBonusBeyond;
	use App\SaleGroupsReach;
	use App\SaleGroupsUsers;
	use App\Service\SelectUserList;
	use App\User;
	use DateTime;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Cache;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Input;
	use Route;
	
	class ProvideController extends BaseController {
		//
		protected $cacheKeyProvide = 'provide.list';
		protected $cacheKeyFinancial = 'provide.view';
		/*判斷獎金發放資料要入本月還是次月 日期*/
		protected $saveDateLine = 6;
		protected $policyModel;
		
		public function __construct () {
			
			parent::__construct();
			
			$this->policyModel = new Provide();
		}
		
		public function list () {
			
			$this->authorize('viewSetting', $this->policyModel);
			
			[ $bonuslist, $saleGroupsReach, $allUserName ] = $this->getProvideListDatas();
			$bonusBeyondList = SaleGroupsBonusBeyond::where('status', 0)->with('saleGroup')->get()->map(function (
				$v
			) {
				$new = $v->toArray();
				$new['user'] = $v->saleGroup->getConvenerUser($v['set_date'], $v['sale_groups_id'])->user->toArray();
				$new['user_name'] = ucfirst($new['user']['name']);
				return $new;
			});
			$bonusBeyondColumns = [
				[ 'data' => 'id' ],
				[ 'data' => 'set_date' ],
				[ 'data' => 'user_name' ],
				[
					'data' => 'sale_group.name'
				],
				[
					'data'   => 'provide_money',
					'render' => '<div data-money="${data}">${data}</div>'
				],
			];
			
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
					'render' => sprintf('<a href="http://%s/jsadwaysN2/campaign_view.php?id=${row.campaign_id}" target="_blank">${row.campaign_name}</a>',
						env('ERP_URL'))
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
			
			return view('financial.provide.list', [
				'data'                   => $this->resources,
				'saleGroupsReach'        => $saleGroupsReach,
				'saleGroupsTableColumns' => $saleGroupsTableColumns,
				'bonuslistColumns'       => $bonuslistColumns,
				'bonusBeyondColumns'     => $bonusBeyondColumns,
				'bonusBeyondList'        => $bonusBeyondList,
				'bonuslist'              => $bonuslist,
				'allUserName'            => $allUserName,
				'autoSelectIds'          => [],
				'total_mondey'           => 0,
			]);
			
		}
		
		public function view () {
			
			
//			            $provideStart = '2021-01-01';
//			            $provideEnd = '2021-01-01';
//			            $saleGroupIds = [1, 2, 3, 4,5,6,7,8];
//			            $userIds = [];
//			            $request = new Request(['startDate' => $provideStart, 'endDate' => $provideEnd, 'saleGroupIds' => $saleGroupIds, 'userIds' => $userIds]);
//			            $datas = $this->getAjaxProvideData($request, 'return');
//			            dd($datas);
			
			[
				$saleGroups,
				$userList
			] = $this->getSelectLists('provide_view');
			
			
			$bonusBeyondColumns = [
				[ 'data' => 'provide_set_date' ],
				[ 'data' => 'set_date' ],
				[ 'data' => 'user.name' ],
				[
					'data' => 'sale_group.name'
				],
				[
					'data'   => 'provide_money',
					'render' => '<div data-money="${data}">${data}</div>'
				],
			];
			
			$provideBonusColumns = [
				[ 'data' => 'provide_set_date' ],
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
			
			return view('financial.provide.view', [
				'data'                   => $this->resources,
				'provideBonusColumns'    => $provideBonusColumns,
				'provideBonus'           => [],
				'saleGroupsReachColumns' => $saleGroupsReachColumns,
				'saleGroupsReach'        => [],
				'saleGroups'             => $saleGroups,
				'bonusBeyondColumns'   => $bonusBeyondColumns,
				'userList'               => $userList
			]);
		}
		
		public function getAllSelectId () {
			$row = FinancialList::where([ 'status' => '0' ])->select('id')->pluck('id');
			
			return $row;
		}
		
		public function ajaxCalculatFinancialBonus () {
			
			$selectFincialIds = Input::post('select_financial_ids') ?? [];
			$selectFincialIds = explode(',', $selectFincialIds);
			
			$financialData = FinancialList::join('users', 'financial_lists.erp_user_id', '=', 'users.erp_user_id')
			                              ->leftJoin('bonus', function ( $join ) {
				                              $join->on('financial_lists.erp_user_id', '=', 'bonus.erp_user_id')
				                                   ->on('financial_lists.set_date', '=', 'bonus.set_date');
			                              })
			                              ->leftJoin('bonus_reach', function ( $join ) {
				                              $join->on('bonus.id', '=', 'bonus_reach.bonus_id');
			                              })
			                              ->leftJoin('financial_provides', function ( $join ) {
				                              $join->on('financial_lists.id', '=',
					                              'financial_provides.financial_lists_id');
			                              })
			                              ->select('financial_provides.created_at as provide_date',
				                              'bonus.id as bonus_id', 'bonus_reach.reach_rate', 'users.name',
				                              'financial_lists.*')
			                              ->whereIn('financial_lists.id', $selectFincialIds)
			                              ->get();
			
			$financialData = $financialData->map(function ( $v, $k ) {
				if ( !empty($v['reach_rate']) && $v['profit'] > 0 ) {
					$exchangeProfitMoney = $this->exchangeMoney($v);
					
					$bonusReach = isset($v->bonus) ? $v->bonus->bonusReach : [];
					$reachRate = $bonusReach->reach_rate ?? 0;
					return $exchangeProfitMoney * $reachRate / 100;
				}
			});
			
			
			echo round($financialData->sum());
		}
		
		public function post ( Request $request ) {
			
			$this->authorize('create', $this->policyModel);
			
			try {
				DB::beginTransaction();
				//Dosomething...
				
				$selectSaleGroupsReachIds = explode(',', $request->provide_sale_groups_bonus);
				
				$this->setSaleGroupsReachProvide($selectSaleGroupsReachIds);
				
				$selectFincialIds = $request->provide_bonus;
				$selectFincialIds = $selectFincialIds != null ? explode(',', $selectFincialIds) : [];
				
				$this->resetFinancialStatus();
				$this->save($selectFincialIds);
				
				
				$createdTime = new DateTime();
				if ( $createdTime->format('d') >= $this->saveDateLine ) {
					$createdTime->modify('+1Month');
				}
				
				$selectSaleGroupsBonusBeyondIds = explode(',', $request->provide_bonus_beyond);
				SaleGroupsBonusBeyond::whereIn('id', $selectSaleGroupsBonusBeyondIds)->update([
					'status' => 1,
					'updated_at' => $createdTime->format('Y-m-01')
				]);
				
				$message['status_string'] = 'success';
				$message['message'] = '更新成功';
				DB::commit();
			} catch ( \Exception $e ) {
				DB::rollback();
				// Handle Error
				$message['message'] = $e->getMessage();
			}
			return view('handle', [
				'message'   => $message,
				'data'      => $this->resources,
				'returnUrl' => Route('financial.provide.list')
			]);
			
			
		}
		
		public function getAjaxProvideData ( Request $request, $outType = 'echo' ) {
			$provideStart = new DateTime($request->startDate);
			$provideEnd = new DateTime($request->endDate);
			$saleGroupIds = $request->saleGroupIds;
			$userIds = $request->userIds;
			
			if ( $provideStart->format('Y-m-01') != $provideEnd->format('Y-m-01') ) {
				$dateRange = date_range($provideStart->format('Y-m-01'), $provideEnd->format('Y-m-01'));
			}
			$dateRange[] = $provideEnd->format('Y-m-01');
			
			if ( !empty($userIds) ) {
				$userIds = User::whereIn('id', $userIds)->get()->pluck('erp_user_id')->toArray();
			}
			
			if ( $saleGroupIds && $userIds == null ) {
				//                $userIds = SaleGroups::with('groupsUsers')->whereIn('id', $saleGroupIds)->get()->map(function ( $v, $k
				//                ) use ( $dateRange ) {
				//                    return $v->groupsUsers->whereIn('set_date', $dateRange)->pluck('erp_user_id');
				//                })->flatten()->unique()->values();
				$userIds = SaleGroupsUsers::whereIn('sale_groups_id', $saleGroupIds)
				                          ->get()
				                          ->pluck('erp_user_id')
				                          ->unique()
				                          ->values();
			}
			
			/*cache start*/
			$cacheData = collect([]);
			$dateNow = new DateTime();
			/*check cache exists*/
			/*cache all user erp Id*/
			$allUserErpIds = Cache::store('memcached')->remember('allUserErpId', ( 24 * 3600 ), function () {
				return User::all()->pluck('erp_user_id')->toArray();
			});
			$cacheObj = new Cachekey();
			foreach ( $dateRange as $date ) {
				$dateTimeObj = new DateTime($date);
				$md5Key = md5($this->cacheKeyFinancial . $date);
				if ( !$cacheObj->has($this->cacheKeyFinancial . $date) ) {
					$saleGroupRowData = $this->getSaleGroupProvide($dateTimeObj, $dateTimeObj, $allUserErpIds, []);
					$bonusRowData = $this->getUserBounsProvide($dateTimeObj, $dateTimeObj, $allUserErpIds, [])
					                     ->where('profit', '<>', 0);
					
					/*TODO::優化快取暫存時間判斷*/
					$dateDistance = round(( $dateNow->getTimestamp() - $dateTimeObj->getTimestamp() ) / ( 3600 * 24 )); //計算資料與現在日期相差幾天
					// 計算快取 releaseTime
					if ( $dateDistance > 45 ) { // over 1.5 month
						$cacheTime = 24 * 365 * 2; // 2 year
					} else { // close one month
						$cacheTime = 1; // 1hr
					};
					
					$cacheObj->put($this->cacheKeyFinancial, $date, [
						'saleGroupRowData' => $saleGroupRowData ?? [],
						'bonusRowData'     => $bonusRowData ?? []
					], $cacheTime);
					
				}
				
				$cacheData[] = Cache::get($md5Key);
			}
			$saleGroupRowData = collect([]);
			$bonusRowData = collect([]);
			
			$cacheData->map(function ( $v, $setDate ) use ( &$saleGroupRowData, &$bonusRowData ) {
				
				$saleGroupRowData = $saleGroupRowData->concat($v['saleGroupRowData']);
				$bonusRowData = $bonusRowData->concat($v['bonusRowData']);
				
			});
			
			/*get provide Bar TrimData*/
			$allName = Bonus::all()->pluck('erp_user_id')->unique()->values();
			
			$provideCharBarStack = $bonusRowData->groupBy('provide_set_date')->map(function ( $v, $k ) {
				$results = $v->groupBy('user_name')->map(function ( $v, $k ) {
					return [
						'provide_money' => $v->sum('provide_money'),
						'erp_user_id'   => $v->max('user.erp_user_id')
					];
				});
				return $results;
			})->map(function ( $items, $k ) use ( $allName ) {
				
				$allName->each(function ( $v, $k ) use ( &$items ) {
					if ( count($items->whereIn('erp_user_id', $v)) == 0 ) {
						$name = ucfirst(User::where('erp_user_id', $v)->first()->name);
						$items[ $name ] = [
							'provide_money' => 0,
							'erp_user_id'   => $v
						];
					}
				});
				return $items->sortByDesc('erp_user_id');
			})->toArray();
			
			
			
			$saleGroupRowData->each(function ( $v, $k ) use ( &$provideCharBarStack ) {
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
				
			});
			
			$bonusBeyondList = SaleGroupsBonusBeyond::where('status', 1)->where('provide_money','!=',0)->whereIn('updated_at',$dateRange)->with('saleGroup')->get()->map(function (
				$v
			) {
				$new = $v;
				$new['user'] = $v->saleGroup->getConvenerUser($v['set_date'], $v['sale_groups_id'])->user->toArray();
				$new['user_name'] = ucfirst($new['user']['name']) ?? '';
				$new['erp_user_id'] = $new['user']['erp_user_id'];
				$new['provide_set_date'] = $v->updated_at->format('Y-m');
				return $new;
			});
			
			$bonusBeyondList->each(function($v) use(&$provideCharBarStack){
				if ( !isset($provideCharBarStack[ $v->provide_set_date ]) ) {
					$provideCharBarStack[ $v->provide_set_date ] = [];
				}
				if ( !isset($provideCharBarStack[ $v->provide_set_date ][ $v->user_name ]) ) {
					$provideCharBarStack[ $v->provide_set_date ][ $v->user_name ] = [];
				}
				if ( !isset($provideCharBarStack[ $v->provide_set_date ][ $v->user_name ]['provide_money']) ) {
					$provideCharBarStack[ $v->provide_set_date ][ $v->user_name ]['provide_money'] = 0;
				}
				$provideCharBarStack[ $v->provide_set_date ][ $v->user_name ]['erp_user_id'] = $v->erp_user_id;
				$provideCharBarStack[ $v->provide_set_date ][ $v->user_name ]['provide_money'] += $v->provide_money;
			});
			
			$provideCharBarStack = collect($provideCharBarStack)->map(function ( $v, $k ) use ( $userIds ) {
				return collect($v)->whereIn('erp_user_id', $userIds);
			});
			
			$provide_groups_list = $saleGroupRowData->whereIn('erp_user_id', $userIds);
			$provide_bonus_list = $bonusRowData->whereIn('erp_user_id', $userIds);
			$provide_bonus_beyond_list = $bonusBeyondList->whereIn('erp_user_id', $userIds);
			
			if ( $saleGroupIds ) {
				$provide_groups_list = $provide_groups_list->whereIn('sale_groups_id', $saleGroupIds);
				$provide_bonus_list = $provide_bonus_list->whereIn('sale_groups_id', $saleGroupIds);
			}
			
			$returnData = [
				"provide_groups_list"    => $provide_groups_list->values()->toArray(),
				"provide_bonus_list"     => $provide_bonus_list->values()->toArray(),
				"provide_char_bar_stack" => $provideCharBarStack->toArray(),
				'provide_bonus_beyond_list' => $provide_bonus_beyond_list->values()->toArray()
			];
			
			if ( $outType == 'echo' ) {
				echo json_encode($returnData);
			} else {
				return $returnData;
			}
			
		}
		
		
		/**
		 * @param $type
		 * @return array
		 */
		public function getSelectLists ( $type ): array {
			
			[ $saleGroups, $userList ] = SelectUserList::getUserList($type);
			
			return [
				$saleGroups,
				$userList
			];
		}
		
		/**
		 * @return array
		 */
		public function getProvideListDatas (): array {
			/*check cache exists*/
			$dataDate = now();
			if ( now()->format('d') < 16 ) {
				$dataDate->modify('-1Month');
			}
			
			$md5Key = md5($this->cacheKeyProvide);
			
			if ( !Cachekey::where('key', $md5Key)->exists() ) {
				/*過濾後勤單位*/
				$erpUSerId = SaleGroups::find(4)->groupsUsers->pluck('erp_user_id')->unique()->values();
				
				$bonuslist = FinancialList::where('status', 1)
				                          ->where('set_date', '<', $dataDate->format('Y-m-01'))
				                          ->where('profit', '<>', 0)
				                          ->whereNotIn('erp_user_id', $erpUSerId)
				                          ->get();
				
				$bonuslist = $bonuslist->map(function ( $v, $k ) {
					$v['sale_group_name'] = $v->saleGroups->saleGroups->name ?? '';
					$v['user_name'] = ucfirst($v->user->name);
					$v['rate'] = $v->bonus->bonusReach->reach_rate ?? 0;
					$v['profit'] = $this->exchangeMoney($v);
					$v['provide_money'] = round($v['profit'] * $v['rate'] / 100);
					$v['set_date'] = substr($v['set_date'], 0, 7);
					/*離職日期*/
					$v['user_resign_date'] = '';
					return $v;
				})->values();
				
				$saleGroupsReach = SaleGroupsReach::where('status', 0)->get();
				
				$saleGroupsReach = $saleGroupsReach->map(function ( $v ) {
					$v->user_name = ucfirst($v->saleUser->user->name);
					$v->group_name = $v->saleGroups->name;
					$v->set_date = substr($v->set_date, 0, 7);
					return $v;
				})->toArray();
				
				$allUserName = collect(array_merge($bonuslist->toArray(), $saleGroupsReach))
					->pluck('erp_user_id', 'user_name')
					->map(function ( $v, $k ) {
						if ( empty($v) ) {
							$v = User::where('name', $k)->first()->erp_user_id;
						}
						return $v;
					});
				
				Cache::forever($md5Key, [
					$bonuslist,
					$saleGroupsReach,
					$allUserName,
				]);
				
				$cacheObj = new Cachekey();
				$cacheObj->key = $md5Key;
				$cacheObj->type = $this->cacheKeyProvide;
				$cacheObj->set_date = now()->format('Y-m-d');
				$cacheObj->release_time = now()->addday(30)->format('Y-m-d H:i:s');
				$cacheObj->save();
			}
			return Cachekey::where('key', $md5Key)->first()->getCacheData();
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
			$financialList->map(function ( $v ) use ( $createdTime ) {
				//save financialList
				$v->status = 2;
				$v->save();
				$v->refresh();
				
				//calculat exchangeProfit
				$exchangeProfitMoney = $this->exchangeMoney($v);
				
				$financial_lists_id = $v->id;
				$bonusReach = isset($v->bonus) ? $v->bonus->bonusReach : [];
				$bonusId = $bonusReach->bonus_id ?? 0;
				$reachRate = $bonusReach->reach_rate ?? 0;
				$provideMoney = $exchangeProfitMoney * $reachRate / 100;
				
				
				$provide = Provide::where('financial_lists_id', $financial_lists_id)->first();
				
				$provideData = [
					'bonus_id'           => $bonusId,
					'financial_lists_id' => $financial_lists_id,
					'provide_money'      => $provideMoney,
					'created_at'         => $createdTime->format('Y-m-01'),
				];
				
				if ( isset($provide) ) {
					//update
					$provide->update($provideData);
				} else {
					//new
					Provide::create($provideData);
				}
				
			});
			
			$this->releaseCache($selectFincialIds);
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
		private function getSaleGroupProvide (
			DateTime $provideStart, DateTime $provideEnd, $userIds = null, $saleGroupIds = null
		) {
			
			if ( $saleGroupIds && $userIds == null ) {
				//                $userIds = SaleGroups::with('groupsUsers')->whereIn('id', $saleGroupIds)->get()->map(function ( $v, $k
				//                ) {
				//                    return $v->groupsUsers->pluck('erp_user_id');
				//                })->flatten();
				$userIds = SaleGroupsUsers::whereIn('sale_groups_id', $saleGroupIds)->get()->pluck('erp_user_id');
			}
			
			/* sale Groups Bonus*/
			$saleGroupsReach = SaleGroupsReach::with('saleGroups', 'saleUser')
			                                  ->where('status', 1)
			                                  ->whereBetween('updated_at', [
				                                  $provideStart->format('Y-m-01'),
				                                  $provideEnd->format('Y-m-31')
			                                  ])
			                                  ->get();
			
			$saleGroupsReach = $saleGroupsReach->whereIn('saleUser.erp_user_id', $userIds);
			$saleGroupsReach = $saleGroupsReach->map(function ( $v, $k ) {
				$v['erp_user_id'] = $v->saleUser->erp_user_id;
				$v['provide_set_date'] = $v->updated_at->format('Y-m');
				$v['user_name'] = ucfirst($v->saleUser->user->name);
				$v['sale_group_name'] = $v->saleGroups->name;
				$v['set_date'] = substr($v['set_date'], 0, 7);
				return $v;
			})->values();
			return $saleGroupsReach;
		}
		
		/**
		 * @param DateTime $provideStart
		 * @param DateTime $provideEnd
		 * @param $userIds
		 * @return FinancialList[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
		 */
		private function getUserBounsProvide (
			DateTime $provideStart, DateTime $provideEnd, $userIds = null, $saleGroupIds = null
		) {
			
			if ( $saleGroupIds && $userIds == null ) {
				//                $userIds = SaleGroups::with('groupsUsers')->whereIn('id', $saleGroupIds)->get()->map(function ( $v, $k
				//                ) {
				//                    return $v->groupsUsers->pluck('erp_user_id');
				//                })->flatten();
				$userIds = SaleGroupsUsers::whereIn('sale_groups_id', $saleGroupIds)->get()->pluck('erp_user_id');
			}
			
			// financial bonus list
			$provideBonus = FinancialList::with([
				'provide',
				'user'
			])->get();
			$provideBonus = $provideBonus->whereBetween('provide.created_at', [
				$provideStart->format('Y-m-01'),
				$provideEnd->format('Y-m-31')
			])->whereIn('erp_user_id', $userIds)->values();
			
			$provideBonus = $provideBonus->map(function ( $v, $k ) {
				$v['sale_group_name'] = isset($v->saleGroups) ? $v->saleGroups->saleGroups->name : '';
				$v['sale_groups_id'] = isset($v->saleGroups) ? $v->saleGroups->sale_groups_id : '';
				$v['user_name'] = ucfirst($v->user->name);
				$v['provide_set_date'] = $v->provide->created_at->format('Y-m');
				$v['provide_money'] = $v->provide->provide_money;
				$v['rate'] = $v->provide->bonusReach->reach_rate ?? 0;
				$v['set_date'] = substr($v['set_date'], 0, 7);
				return $v;
			})->values();
			return $provideBonus;
		}
		
		/**
		 * @param array $selectSaleGroupsReachIds
		 */
		private function setSaleGroupsReachProvide ( array $selectSaleGroupsReachIds ): void {
			$saleGroupReach = new SaleGroupsReach();
			$createdTime = new DateTime();
			if ( $createdTime->format('d') >= $this->saveDateLine ) {
				$createdTime->modify('+1Month');
			}
			
			$saleGroupReach->whereIn('id', $selectSaleGroupsReachIds)->update([
				'status'     => 1,
				'updated_at' => $createdTime->format('Y-m-01')
			]);
		}
		
		private function getProvideBalanceSelectedId ( $dataList ) {
			$dataList = $dataList->groupBy('erp_user_id');
			$selectIds = $dataList->map(function ( $v, $erpUserId ) {
				/*TODO::離職日*/
				$isAlive = '0000-00-00' == '0000-00-00';
				if ( $isAlive && $v->sum('provide_money') >= 0 ) {
					return $v->pluck('id');
				}
			})->filter()->flatten();
			
			return $selectIds;
		}
		
		/**
		 * @param array $selectFincialIds
		 */
		private function releaseCache ( array $selectFincialIds ): void {
			$releaseCacheDate = new DateTime();
			if ( $releaseCacheDate->format('d') >= $this->saveDateLine ) {
				$releaseCacheDate->modify('+1Month');
			}
			
			$financialSetDate = FinancialList::whereIn('id', $selectFincialIds)
			                                 ->get()
			                                 ->pluck('set_date')
			                                 ->unique()
			                                 ->values();
			
			$cacheObj = CacheKey::where('type', $this->cacheKeyProvide)
			                    ->orWhere('type', $this->cacheKeyFinancial)
			                    ->orWhere(function ( $query ) use (
				                    $releaseCacheDate
			                    ) {
				                    $query->where('set_date', $releaseCacheDate->format('Y-m-d'))
				                          ->where('type', $this->cacheKeyFinancial);
			                    })
			                    ->orWhere(function ( $query ) use ( $financialSetDate ) {
				                    $query->where('type', 'financial.review')->whereIn('set_date', $financialSetDate);
			                    })
			                    ->get();
			
			CacheKey::releaseCacheByDatas($cacheObj);
		}
	}


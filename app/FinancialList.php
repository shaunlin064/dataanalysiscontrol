<?php
	
	
	namespace App;
	
	use App\Http\Controllers\FinancialController;
	use DateTime;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Facades\Cache;
	use Illuminate\Support\Facades\DB;
	
	class FinancialList extends Model {
		//
		
		protected $guarded = [
			'med_group',
			'dep_name',
			'dep_id',
			'username',
			'priority',
			'accounting_id',
			'agency_name',
			'client_name',
			'companies_name'
		];
		
		protected $attributes = [
			'status'            => 0,
			'profit_percentage' => 0,
			'companies_id'      => 0,
		];
		
		protected $keyReplace = [
			'memberid'    => 'erp_user_id',
			'currency_id' => 'currency',
			'year_month'  => 'set_date',
			'cam_id'      => 'campaign_id',
			'o_id'        => 'cp_detail_id'
		];
		
		public function __construct () {
			parent::__construct();
		}
		
		public function bonus () {
			return $this->hasOne(Bonus::CLASS, 'erp_user_id', 'erp_user_id')->where('set_date', $this->set_date);
		}
		
		public function provide () {
			return $this->hasOne(Provide::CLASS, 'financial_lists_id', 'id');
		}
		
		public function receipt () {
			return $this->hasOne(FinancialReceipt::CLASS, 'financial_lists_id', 'id');
		}
		
		public function user () {
			return $this->hasOne(User::CLASS, 'erp_user_id', 'erp_user_id');
		}
		
		public function saleGroups () {
			
			return $this->hasOne(SaleGroupsUsers::CLASS, 'erp_user_id', 'erp_user_id')
			            ->where('set_date', $this->set_date);
		}
		
		public function agency () {
			return $this->belongsTo(Agency::CLASS, 'agency_id');
		}
		
		public function client () {
			return $this->belongsTo(Client::CLASS);
		}
		
		public function companies () {
			return $this->belongsTo(Companie::CLASS);
		}
		
		public function campaignProjectManager () {
			return $this->hasMany(CampaignProjectManager::Class, 'campaign_id', 'campaign_id');
		}
		
		public function customerSaveAndUpdate ( array $dates ) {
			$checkColumnpPimaryKey = [
				'companies_name' => 'companies_id',
				'agency_name'    => 'agency_id',
				'client_name'    => 'client_id'
			];
			foreach ( $checkColumnpPimaryKey as $nameColumn => $pimaryKeyColumn ) {
				collect($dates)->filter(function ( $v ) use ( $pimaryKeyColumn ) {
					return $v[ $pimaryKeyColumn ] != null;
				})->pluck($nameColumn, $pimaryKeyColumn)->each(function ( $name, $id ) use ( $nameColumn ) {
						switch ( $nameColumn ) {
							case 'companies_name':
								$res = \App\Companie::firstOrNew([ 'id' => $id ]);
								break;
							case 'agency_name':
								$res = \App\Agency::firstOrNew([ 'id' => $id ]);
								break;
							case 'client_name':
								$res = \App\Client::firstOrNew([ 'id' => $id ]);
								break;
						}
						$res->name = $name ?? 'Error名稱錯誤';
						$res->save();
					});
			}
			
			return $this;
		}
		
		public function dataFormat () {
			$this->keyChange()->valueChange();
			return $this;
		}
		
		public function rawKeyChange ( $data ) {
			foreach ( $data as $index => $attribute ) {
				
				if ( in_array($index, array_keys($this->keyReplace)) ) {
					
					$key = $this->keyReplace[ $index ];
					$data[ $key ] = $attribute;
					unset($data[ $index ]);
				}
			}
			return $data;
		}
		
		public function keyChange () {
			$attributes = $this->getAttributes();
			
			$this->setRawAttributes($this->rawKeyChange($attributes));
			
			return $this;
		}
		
		public function revertKeyChange () {
			
			$attributes = $this->getAttributes();
			
			foreach ( $attributes as $index => $attribute ) {
				
				if ( in_array($index, $this->keyReplace) ) {
					
					$key = array_flip($this->keyReplace)[ $index ];
					$attributes[ $key ] = $attribute;
					unset($attributes[ $index ]);
					
				}
			};
			$this->setRawAttributes($attributes);
			
			return $this;
		}
		
		public function valueChange () {
			$this->set_date = date('Y-m-d', strtotime($this->set_date . '01'));
			if ( $this->profit_percentage == null ) {
				$this->profit_percentage = 0;
			}
			return $this;
		}
		
		public function revertValueChange () {
			$this->year_month = date('Ym', strtotime($this->year_month));
			if ( $this->profit_percentage == null ) {
				$this->profit_percentage = 0;
			}
			return $this;
		}
		
		public function updateSet ( $id, $data ) {
			$data = $this->rawKeyChange($data);
			
			if ( $data['profit_percentage'] == null ) {
				$data['profit_percentage'] = 0;
			}
			//guarded
			foreach ( $this->guarded as $unsetField ) {
				unset($data[ $unsetField ]);
			}
			// ex guarded
			unset($data['set_date']);
			unset($data['cp_detail_id']);
			unset($data['campaign_id']);
			
			if ( $this->checkDiff($id, $data) ) {
				return $this->where('id', $id)->update($data);
			}
		}
		
		/**
		 * @param $v
		 * @return mixed
		 */
		function setStatusTime ( $v ) {
			$v['paymentStatus'] = isset($v['receipt']['created_at']) ? substr($v['receipt']['created_at'], 0,
				10) : 'no';
			$v['bonusStatus'] = isset($v['provide']['updated_at']) ? substr($v['provide']['updated_at'], 0, 10) : 'no';
			return $v;
		}
		
		/**
		 * @param $id
		 * @param string $dateStart
		 * @param string $dateEnd
		 * @return bool|mixed|string
		 */
		public function getFinancialData ( array $erpUserIds, string $dateStart, string $dateEnd = null ) {
			$dateStart = new \DateTime($dateStart);
			$dateEnd = $dateEnd ? new \DateTime($dateEnd) : $dateStart;
			
			$erpReturnData = $this->whereIn('erp_user_id', $erpUserIds)
			                      ->whereBetween('set_date',
				                      [ $dateStart->format('Y-m-01'), $dateEnd->format('Y-m-01') ])
			                      ->where('profit', '!=', 0)
			                      ->with('receipt')
			                      ->with('provide')
			                      ->get()
			                      ->map(function ( $v ) {
				
				                      $v = $this->setStatusTime($v);
				                      $v->sale_group_name = $v->saleGroups->saleGroups->name ?? '';
				                      $v->user_name = ucfirst($v->user->name);
				                      $v->sale_group_id = $v->saleGroups->sale_groups_id ?? 0;
				                      return $this->exchangeMoney($v);
			                      })
			                      ->values()
			                      ->toArray();
			
			return $erpReturnData;
		}
		
		//存入所有資料 資料重抓使用 （慎）
		public function saveUntilNowAllData () {
			$financial = new FinancialController();
			$erpReturnData = collect($financial->getErpMemberFinancial([ 'all' ], 'all'))->whereIn('organization',
				[ 'js', 'ff' ]);
			
			DB::beginTransaction();
			try {
				$newdata = [];
				foreach ( $erpReturnData as $item ) {
					$financeList = new FinancialList();
					$financeList->fill($this->rawKeyChange($item))->dataFormat()->save();
				}
				$this->customerSaveAndUpdate($erpReturnData->toArray());
				DB::commit();
				
			} catch ( \Exception $ex ) {
				DB::rollback();
				dd($ex->getMessage());
			}
		}
		
		//存入現在已完成結帳月份的資料
		public function saveCloseData ( $yearMonthDayStr = null ) {
			$financial = new FinancialController();
			
			if ( empty($yearMonthDayStr) ) {
				$date = new DateTime(date('Ym01'));
			} else {
				$date = new DateTime($yearMonthDayStr);
			}
			$erpReturnData = collect($financial->getErpMemberFinancial([ 'all' ],
				$date->format('Ym')))->whereIn('organization', [ 'js', 'ff' ]);
			
			$createReturnData = $erpReturnData->filter(function ( $v, $k ){
				$tmpDate = new DateTime($v['year_month'] . '01');
				$setDate = $tmpDate->format('Y-m-d');
				$searchData = $this->where([ 'cp_detail_id' => $v['o_id'], 'set_date' => $setDate ]);
				
				if ( $searchData->exists() ) {
					$this->updateSet($searchData->first()['id'], $v);
				} else {
					return $v;
				}
			});
			
			/*刪除作廢案件資料*/
			$erpReturnVoidData = collect($financial->getErpMemberCancelFinancial([ 'all' ], $date->format('Ym')))
				->pluck('o_id')
				->toArray();
			
			DB::beginTransaction();
			try {
				$createReturnData->each(function ( $v ) {
					$financeList = new FinancialList();
					$financeList->fill($this->rawKeyChange($v))->dataFormat()->save();
				});
				
				$this->whereIn('cp_detail_id', $erpReturnVoidData)->delete();
				
				/*更新資料*/
				$this->customerSaveAndUpdate($erpReturnData->toArray());
				
				$this->projectManagerUpdateOrCreate($erpReturnData->pluck('cam_id')->toArray());
				
				DB::commit();
				
			} catch ( \Exception $ex ) {
				DB::rollback();
				dd(\Log::error($ex->getMessage()));
			}
		}
		
		public function projectManagerUpdateOrCreate ( $campaigIds ) {
			if(count($campaigIds) > 0){
				$project = new \App\CampaignProjectManager();
				$results = collect($project->getErpProjectManagerData($campaigIds))->groupBy('campaign_id')->toArray();
				
				foreach ( $results as $campaignId => $items ) {
					$fina = \App\FinancialList::where('campaign_id', $campaignId)->first();
					$fina->campaignProjectManager()->delete();
					$fina->campaignProjectManager()->createMany($items);
				}
			}
		}
		
		public function exchangeMoney ( $items ) {
			$set_date = $items->set_date ?? date('Y-m-01', strtotime($items['year_month'] . '01'));
			$currency = $items->currency ?? $items['currency_id'];
			$organization = $items->organization ?? $items['organization'];
			
			
			$exchangeRate = ExchangeRate::where([ 'set_date' => $set_date, 'currency' => $currency ])->first();
			
			//台灣傑思後台 只能填 台幣
			if ( $organization == 'js' ) {
				return $items;
			};
			
			if ( empty($exchangeRate) ) {
				switch ( $currency ) {
					case 'USD':
						$exchangeRate = 31;
						break;
					case 'JPY':
						$exchangeRate = 0.2875;
						break;
				}
			} else {
				$exchangeRate = $exchangeRate->rate;
			}
			if ( in_array($currency, [ 'USD', 'JPY' ]) ) {
				$tmpData = [
					'income' => round(( $items->income ?? $items['income'] ) * $exchangeRate),
					'cost'   => round(( $items->cost ?? $items['cost'] ) * $exchangeRate),
				];
				$tmpData['profit'] = $tmpData['income'] - $tmpData['cost'];
				
				//TODO 毛利小於零 是否需要扣1%
				if ( $items->organization ?? $items['organization'] == 'hk' ) {
					$tmpData['profit'] = round($tmpData['profit'] * 0.99);
				}
				switch ( gettype($items) ) {
					case 'object':
						foreach ( $tmpData as $key => $item ) {
							$items->$key = $item;
						}
						break;
					default:
						foreach ( $tmpData as $key => $item ) {
							$items[ $key ] = $item;
						}
				}
			}
			return $items;
			
		}
		
		public function getUserLatelyProfit ( $erpUserId ) {
			$thisMonth = new \DateTime();
			$thisMonth = $thisMonth->format('Y-m-01');
			
			$lastMonth = new \DateTime('last day of last month');
			$lastMonth = $lastMonth->format('Y-m-01');
			
			$allProfitBySetDate = $this->where('erp_user_id', $erpUserId)
			                           ->where('profit', '<>', 0)
			                           ->get()
			                           ->map(function ( $v ) { return $this->exchangeMoney($v); })
			                           ->groupBy('set_date')
			                           ->map(function ( $item ) { return $item->sum('profit'); });
			
			$thisMonthProfit = $allProfitBySetDate->get($thisMonth) ?? 0;
			$lastMonthProfit = $allProfitBySetDate->get($lastMonth) ?? 0;
			$highestProfit = empty($allProfitBySetDate) ? 0 : $allProfitBySetDate->max();
			
			return [ $highestProfit, $thisMonthProfit, $lastMonthProfit ];
		}
		
		public function getDataList ( $fieldName, $fieldid ) {
			
			$data = Cache::store('memcached')
			             ->remember($fieldName, ( 1 * 3600 ), function () use ( $fieldName, $fieldid ) {
				             return $this->all()->pluck($fieldName, $fieldid)->filter()->unique()->map(function (
					             $v, $id
				             ) {
					             $tmp = [ 'name' => $v, 'id' => $id ];
					             return $tmp;
				             })->sortBy('name')->values()->toArray();
			             });
			
			return $data;
		}
		
		
		/**
		 * @param $id
		 * @param $data
		 * @return mixed
		 */
		private function checkDiff ( $id, $data ) {
			$orginal = collect($this->where('id', $id)->first()->toArray());
			$orginal->forget('set_date');
			$orginal->forget('cp_detail_id');
			$orginal->forget('campaign_id');
			$orginal->forget('id');
			$orginal->forget('status');
			$orginal->forget('created_at');
			$orginal->forget('updated_at');
			return count($orginal->diffAssoc($data)) > 0 ? true : false;
		}
	}

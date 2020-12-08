<?php
	
	
	namespace App;
	
	use DateTime;
	use Illuminate\Database\Eloquent\Model;
	
	class SaleGroups extends Model {
		//
		protected $fillable = [ 'name' ];
		
		/**
		 * @param $setDate
		 * @param $saleGroupId
		 * @return array
		 */
		static function getConvenerUser ( $setDate, $saleGroupId ) {
			return SaleGroups::find($saleGroupId)->groupsUsers->where('is_convener', 1)
			                                                  ->where('set_date', $setDate)
			                                                  ->first();
		}
		
		public function groupsUsers () {
			
			return $this->hasMany(SaleGroupsUsers::CLASS)->orderBy('set_date', 'desc')->orderBy('is_convener', 'desc');
			
		}
		
		public function saleGroupsBonusBeyond () {
			return $this->hasMany(SaleGroupsBonusBeyond::Class);
		}
		
		public function groupsUsersLastMonth () {
			$lastMonth = date('Y-m-01', strtotime("-1 month"));
			
			return $this->hasMany(SaleGroupsUsers::CLASS)
			            ->where('set_date', $lastMonth)
			            ->orderBy('is_convener', 'desc');
		}
		
		public function groupsBonus () {
			return $this->hasMany(SaleGroupsBonusLevels::CLASS)->orderBy('set_date', 'desc');
		}
		
		public function groupsBonusLastMonth () {
			$lastMonth = date('Y-m-01', strtotime("-1 month"));
			
			return $this->hasMany(SaleGroupsBonusLevels::CLASS)
			            ->where('set_date', $lastMonth)
			            ->orderBy('set_date', 'desc');
		}
		
		public function saleGroupsRate () {
			return $this->hasMany(SaleGroupsRate::Class);
		}
		
		/**
		 * @param $saleGroupIds
		 * @return array
		 */
		public function getGroupBoundary ( $saleGroupIds, string $dateTime ) {
			$tmpGroups = SaleGroups::whereIn('id', $saleGroupIds)->get()->map(function ( $v, $k ) use ( $dateTime ) {
				$dateStart = new DateTime($dateTime);
				$dateStart = $dateStart->format('Y-m-01');
				$sameGroupErpUserIds = SaleGroupsUsers::where([ 'sale_groups_id' => $v->id, 'set_date' => $dateStart ])
				                                      ->get();
				$groupBoundary = $sameGroupErpUserIds->map(function ( $v, $k ) {
					$v['boundary'] = $v->getUserBonusBoundary->boundary ?? 0;
					return $v;
				})->sum('boundary');
				$v['boundary'] = $groupBoundary;
				$v['set_date'] = $dateStart;
				return $v;
			});
			return $tmpGroups->toArray();
		}
		
		public function getGroupExtraBonus ( $setDate ) {
			
			$profit = SaleGroupsReach::getProfit($this->id, $setDate);
			$boundary = $this->getGroupBoundary([ $this->id ], $setDate)[0]['boundary'];
			$percentage = ( $profit == 0 || $boundary == 0 ) ? 0 : round($profit / $boundary * 100);
			$bonusMembersBeyondMoney = $this->getBonusMemberBeyondMoney($setDate);
			$bonusDirect = SaleGroupsReach::getBonusDirect($this->id, $setDate, $percentage);
			
			return [ $profit, $percentage, $bonusDirect, $bonusMembersBeyondMoney ];
		}
		
		/**
		 * @param $setDate
		 * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|int|mixed
		 */
		private function getBonusMemberBeyondMoney ( $setDate ) {
			$bonusMembersBeyondMoney = 0;
			$passFlag = 0;
			
			if ( in_array($this->id, config('sale_group.bonusMembersBeyondSetSaleGroupIds')) ) {
				/*find specify saleGroup*/
				$thisGroupMember = $this->groupsUsers->where('set_date', $setDate)->values();
				
				/*find convener user*/
				$thisGroupConvener = SaleGroups::getConvenerUser($setDate, $this->id);
				
				if ( $thisGroupMember->count() && $thisGroupConvener->count() ) {
					/* exclude convener user*/
					$thisGroupMember = $thisGroupMember->reject(function ( $v ) use ( $thisGroupConvener ) {
						return $v['erp_user_id'] == $thisGroupConvener['erp_user_id'];
					})->values();
					
					$thisGroupMember->map(function ( $v ) use ( &$passFlag, &$bonusMembersBeyondMoney ) {
						
						$result = \App\SaleGroupsReach::getBonusMembersBeyondMoney($v['set_date'], $v['erp_user_id']);
						$bonusMembersBeyondMoney += $result['bonus_direct'];
						$passFlag += $result['reach_level_rate'] >= 100 ? 1 : 0;
						
					});
					/*計算是否全員都達到100 額外加bonus*/
					if ( $passFlag === $thisGroupMember->count() ) {
						$result = \App\SaleGroupsReach::getBonusMembersBeyondMoney($thisGroupConvener['set_date'] . '-01',
							$thisGroupConvener['erp_user_id']);
						if ( $result['reach_level_rate'] >= 100 ) {
							$bonusMembersBeyondMoney += config('sale_group.bonusMembersBeyondLevel.extra_bonus');
						}
					}
				}
				
			}
			return $bonusMembersBeyondMoney;
		}
	}

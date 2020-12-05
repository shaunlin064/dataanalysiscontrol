<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2020/12/4
	 * Time: 10:11
	 */
	
	
	namespace App\Service;
	
	
	use App\Bonus;
	use App\Permission;
	use App\SaleGroups;
	use App\SaleGroupsUsers;
	use App\User;
	use DateTime;
	
	class SelectUserList {
		
		/**
		 * @param $type
		 * @return array
		 */
		static function getUserList ( $type ) {
			
			$erpUserId = auth()->user()->erp_user_id;
			$date = new DateTime();
			$dateStr = $date->format('Y-m-01');
			/*convener check*/
			$saleGroupsUsers = SaleGroupsUsers::where([
				'erp_user_id' => $erpUserId,
				'set_date'    => $dateStr
			])->first();
			
			/* 依照權限不同 取的 user list 資料差異
					admin 全取
					convener 取該團隊
					user 取自己
					*/
			$userList = collect([]);
			if ( Permission::canReviewFinancialAllUserData($type) ) {
				
				$saleGroups = SaleGroups::all();
				$userList = Bonus::with('user')->groupBy('erp_user_id')->orderBy('erp_user_id')->get()->map(function (
					$v
				) {
					
					if ( isset($v->user) ) {
						$newUser = $v->user;
						$newUser->name = ucfirst($newUser->name);
					} else {
						$newUser = new User();
						$newUser->erp_user_id = $v->erp_user_id;
					}
					return $newUser;
				});
				
			} else {
				if ( Permission::canReviewFinancialGroupData($type) ) {
					
					$saleGroups = [ $saleGroupsUsers->saleGroups ];
					
					$saleGroupErpUserIds = $saleGroupsUsers->getSameGroupsUser($erpUserId, $dateStr)
					                                       ->pluck('user')
					                                       ->pluck('erp_user_id')
					                                       ->toArray();
					$userList = User::whereIn('erp_user_id', $saleGroupErpUserIds)->get();
				} else {
					$saleGroups = [];
					$userList[] = auth()->user();
				}
			}
			return [
				$saleGroups,
				$userList->toArray()
			];
		}
		
		static function getPmList () {
			$pmRole = [
				'pm',
				'all_pm_supervisor',
				'media_pm_supervisor',
				'kol_pm_supervisor',
				'editor_pm_supervisor'
			];
			/*區分一般 非pm user 直接提供全部PM List*/
			if ( !auth()->user()->roles->whereIn('name', $pmRole)->count() || auth()
					->user()
					->hasPermission('bonus.review.all_pm') ) {
				return $pmList = \App\CampaignProjectManager::with('user')->groupBy('erp_user_id')->get()->toArray();
			}
			
			$permissions = [
				'bonus.review.media_all_pm',
				'bonus.review.kol_all_pm',
				'bonus.review.editor_all_pm',
			];
			
			
			
			foreach ( $permissions as $permission ) {
				$queryBuild = \App\CampaignProjectManager::with('user');
				if ( auth()->user()->hasPermission($permission) ) {
					switch ( $permission ) {
						case 'bonus.review.media_all_pm':
							$pmList[] = $queryBuild->where('type',1);
							break;
						case 'bonus.review.kol_all_pm':
							$pmList[] = $queryBuild->where('type',2);
							break;
						case 'bonus.review.editor_all_pm':
							$pmList[] = $queryBuild->where('type',3);
							break;
					}
				}
			}
			if(empty($pmList)){
				$pmList[] = $queryBuild->where('erp_user_id',auth()->user()->erp_user_id);
			}
			
			$resultList = collect([]);
			foreach ($pmList as $item) {
				$resultList = $resultList->merge($item->groupBy('erp_user_id')->get()->values()->toArray());
			}
			
			return $resultList->toArray();
		}
	}
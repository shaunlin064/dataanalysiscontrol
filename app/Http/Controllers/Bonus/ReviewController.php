<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-20
	 * Time: 10:15
	 */
	
	namespace App\Http\Controllers\Bonus;
	
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
	use Illuminate\Support\Facades\Input;
	use Auth;
	
	class ReviewController extends BaseController
	{
//
		public function __construct ()
		{
			parent::__construct();
		}
		
		public function list ()
		{
			
			$listdata = Bonus::where('id','!=','0')->groupBy('erp_user_id')->get()->map(function($v,$k){
				
				$v->name =  ucfirst($v->user->name);
				
				$groupNames = $v->user->userGroups->map(function($v,$k){
					return $v->saleGroups->name;
				})->toArray();
				
				$v->sale_groups_name = isset($groupNames) ? implode(',', $groupNames) : '';
				
				return $v;
			})->toArray();
			
			return view('bonus.review.list',['data' => $this->resources,'row' => $listdata]);
		}
		
		public function view($erpUserId = null)
		{
			
			$loginUserId = Auth::user()->erp_user_id;
			
			$erpUserId = $erpUserId ?? $loginUserId;
			
			//return error
			if(isset(session('users')[$erpUserId]) == false){
				abort(404);
			}
			
			//check permission
			$permission = new Permission();
			$permission->permissionCheck($erpUserId,$loginUserId);
			
			$date = new \DateTime();
			
			
			$provideObj = new ProvideController();
			
			list($saleGroups, $userList) = $provideObj->getListData($loginUserId, $date);
			
			$bonusColumns =
			 [
				['data'=> 'set_date'],
				['data'=> 'user_name'],
				['data'=> 'sale_group_name'],
				['data'=> 'campaign_name','render' => sprintf('<a href="http://%s/jsadwaysN2/campaign_view.php?id=${row.campaign_id}" target="_blank">${row.campaign_name}</a>',env('ERP_URL'))],
				['data'=> 'media_channel_name'],
				['data'=> 'sell_type_name'],
			  ['data'=> 'currency'],
			  ['data'=> 'organization'],
			  ['data'=> 'income'],
			  ['data'=> 'cost'],
				['data'=> 'profit' , 'render' => '<p style="${style}">${row.profit}</p>','parmas' => 'let style = row.organization == "hk" ? "color:red" : "" '],
				['data'=> 'paymentStatus'],
				['data'=> 'bonusStatus'],
			 ];

			////
			//$dateStart =  $date->format('2017-01-01');
			//$dateEnd = $date->format('Y-m-01');
			////$userIds = collect($userList)->pluck('erp_user_id')->toArray();
			//$request = new Request(['startDate' => $dateStart,'endDate'=>$dateEnd,'saleGroupIds' => [1,2,3,4],'userIds'=>null]);
			//$this->getAjaxData($request);
		
			$labels = [];
			$chartDataBar = [
			 [
				'data' => 0,
			 ],
			 [
				'data' => 0,
			 ],
			 [
				'data' => 0,
			 ],
			];
			$chartData = [
			  [ 'data' => [
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
			 ['data'=> 'set_date',"width"=>"50px"],
				['data' => 'user_name',"width"=>"50px"],
			 ['data'=> 'sale_group_name',"width"=>"50px"],
				['data' => 'bonus_next_percentage' , 'render' => '<span style="display: none">${data}</span><div class="progress progress-xs progress-striped active" style="${rotate}"><div class="progress-bar progress-bar-${style}" style="width: ${Math.abs(data)}%;"></div></div>','parmas' => 'let style="yellow"; let rotate=""; if(data > 90){ style = "success"}else if(data < 0){ style = "danger"; rotate = "transform: rotate(180deg)";}'],
			 ['data' => 'bonus_next_percentage','render' => '<span style="display: none">${style}</span><span class="badge bg-${style}">${data}%</span>','parmas' => 'let style ="yellow"; if(data > 90){ style = "green"}else if(data < 0){ style = "red"}',"width"=>"10px"],
			 ['data' => 'bonus_rate',"width"=>"20px",'render' => '${data}%'],
			 ['data' => 'profit'],
			];
			
			$groupsProgressColumns = [
			 ['data'=> 'set_date',"width"=>"50px"],
			 ['data'=> 'name',"width"=>"50px"],
			 ['data' => 'percentage' , 'render' => '<span style="display: none">${data}</span><div class="progress progress-xs progress-striped active" style="${rotate}"><div class="progress-bar progress-bar-${style}" style="width: ${Math.abs(data)}%;"></div></div>','parmas' => 'let style="yellow"; let rotate=""; if(data > 90){ style = "success"}else if(data < 0){ style = "danger"; rotate = "transform: rotate(180deg)";}'],
			 ['data' => 'percentage','render' => '<span style="display: none">${style}</span><span class="badge bg-${style}">${data}%</span>','parmas' => 'let style ="yellow"; if(data > 90){ style = "green"}else if(data < 0){ style = "red"}',"width"=>"10px"],
				['data' => 'profit']
			 ];
	
			
			return view('bonus.review.view',[
				'data' => $this->resources,
				'chartData' => $chartData,
				'chartDataBar' => $chartDataBar,
				'chartDataBarLabels' => $labels,
				'bonusData' => [],
				'bonusColumns' => $bonusColumns,
				'progressDatas' => [],
				'progressColumns' => $progressColumns,
				'groupProgressDatas' => [],
				'groupProgressColumns' => $groupsProgressColumns,
				'saleGroups' => $saleGroups,
				'userList' => $userList
			]
			);
		}
		
		public function getAjaxData (Request $request,$outType = 'echo')
		{
			$dateStart = $request->startDate;
			$dateEnd = $request->endDate;
			$saleGroupIds = $request->saleGroupIds;
			$userIds = $request->userIds;
			
			$SaleGroupsObj = new SaleGroups();
			if(!empty($userIds)){
				$userIds = User::whereIn('id',$userIds)->get()->pluck('erp_user_id')->toArray();
			}
			
			if($saleGroupIds && empty($userIds)){
				$userIds = $SaleGroupsObj->with('groupsUsers')->whereIn('id', $saleGroupIds)->get()->map(function ($v, $k) {
					return $v->groupsUsers->pluck('erp_user_id');
				})->flatten()->unique()->values()->toArray();
			}
			
			/*cache all user erp Id*/
			$allUserErpIds = Cache::store('memcached')->remember('allUserErpId', (4*360), function () {
				return User::all()->pluck('erp_user_id')->toArray();
			});
			/*cache all groupId*/
			$allGroupIds = Cache::store('memcached')->remember('allGroupId', (10*60), function () {
			return SaleGroups::all()->pluck('id')->toArray();
		});
			
			/*cache start*/
			if( $dateStart != $dateEnd){
				$dateRange = date_range($dateStart,$dateEnd);
			}
			$dateRange[] = $dateEnd;
			$cacheData = collect([]);
			$dateNow = new DateTime();
			/*check cache exists*/
			$cahceKey = 'financial.review';
			foreach($dateRange as $date){
				/*TODO::優化快取暫存時間判斷*/
				$date2 = new DateTime($date);
				$cacheTime = 1;//hr
				$dateDistance = ($dateNow->getTimestamp()-$date2->getTimestamp())/(60*60*24)/365;
				if($dateDistance > 2){ // over two year
					$cacheTime = 24 * 30; // 1 month
				}elseif($dateDistance > 1){ // over one year
					$cacheTime = 24 * 15; // 2 week
				}elseif($dateDistance > 0.125){ // over 1.5 month
					$cacheTime = 24; // 1 day
				}else{ // close one month
					$cacheTime = 1; // 1 hr
				};
				
				if (!Cache::store('memcached')->has($cahceKey.$date)) {
					//
					list($erpReturnData, $progressDatas, $groupProfitDatas) = $this->getDataFromDataBase($allUserErpIds, $allGroupIds,$date, $date);
					Cache::store('memcached')->put($cahceKey.$date, ["bonus_list" =>$erpReturnData, 'progress_list' => $progressDatas, 'group_progress_list' => $groupProfitDatas],($cacheTime * 3600 ));
				}
				$cacheData[] = Cache::store('memcached')->get($cahceKey.$date);
			}
			
			$group_progress_list = collect([]);
			$bonus_list = collect([]);
			$progress_list = collect([]);
			$cacheData->map(function($v,$setDate) use(&$progress_list,&$bonus_list,&$group_progress_list){
				//$bonus_list = array_merge($bonus_list,$v['bonus_list']->toArray());
				$bonus_list = $bonus_list->concat($v['bonus_list']);
				$progress_list = $progress_list->concat($v['progress_list']);
				$group_progress_list = $group_progress_list->concat($v['group_progress_list']);
			});
			$bonus_list = $bonus_list->whereIn('erp_user_id',$userIds);
			
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
			$bonus_list->groupBy('set_date')->map(function ($v, $k) use (&$chartFinancialBar) {
				$tmpDate = new DateTime($k);
				$chartFinancialBar['labels'][] = $tmpDate->format('Ym');
				$chartFinancialBar['totalIncome'][] = round($v->sum('income'));
				$chartFinancialBar['totalCost'][] = round($v->sum('cost'));
				$chartFinancialBar['totalProfit'][] = round($v->sum('profit'));
			});
			$bonus_list = $bonus_list->values()->toArray();
			$progress_list = $progress_list->whereIn('erp_user_id',$userIds)->values()->toArray();
			$group_progress_list = $group_progress_list->whereIn('id',$saleGroupIds)->values()->toArray();
			
			if($outType == 'echo'){
				echo json_encode(["bonus_list" => $bonus_list, "chart_money_status" => $chartMoneyStatus, "chart_financial_bar" => $chartFinancialBar, 'progress_list' => $progress_list, 'group_progress_list' => $group_progress_list]);
			}
			
			
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
				
				$erpReturnData = $erpReturnData->map(function($items){
					$items = gettype($items) == 'object' ? get_object_vars($items) : $items;
				
					if ($items['income'] != 0 || $items['cost'] != 0) {

						return $items;
					}
				})->filter()->values()->toArray();
			}
			return array($totalIncome, $totalCost, $totalProfit, $erpReturnData);
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
			 'profit' => $returnBonusData['estimateBonus'],
			 'bonus_rate' => isset($returnBonusData['reachLevle']['bonus_rate']) ? $returnBonusData['reachLevle']['bonus_rate'] : 0,
			 'bonus_next_amount' => isset($returnBonusData['nextLevel']['bonus_next_amount']) ? round($returnBonusData['nextLevel']['bonus_next_amount']) : 0,
			 'bonus_next_percentage' => isset($returnBonusData['nextLevel']['bonus_next_percentage']) ? $returnBonusData['nextLevel']['bonus_next_percentage'] : 0,
			 'bonus_direct' => $returnBonusData['bonusDirect']
			];
			
			return $boxData;
		}
		
		/**
		 * @param $id
		 * @param string $yearMonthDay
		 * @return bool|mixed|string
		 */
		private function getFinancialData (Array $erpUserIds, string $dateStart,string $dateEnd)
		{
			$financialListObj = new FinancialList();
			return $financialListObj->getFinancialData($erpUserIds, $dateStart,$dateEnd);
		}
		
		/**
		 * @param array $userIds
		 * @param $dateStart
		 * @param $dateEnd
		 * @param SaleGroups $SaleGroupsObj
		 * @param $saleGroupIds
		 * @return array
		 * @throws \Exception
		 */
		private function getDataFromDataBase (array $userIds, $saleGroupIds, $dateStart, $dateEnd ): array
		{
			$SaleGroupsObj = new SaleGroups();
			
			$erpReturnData = collect($this->getFinancialData($userIds, $dateStart, $dateEnd));
			
			/*progressDatas*/
			$progressDatas = collect([]);
			$erpReturnData->groupBy(['set_date', 'erp_user_id'])->map(function ($items, $setDate) use (&$progressDatas) {
				
				$items = $items->map(function ($v, $erpUserId) use ($setDate) {
					$tmpData = $this->getUserBonus($erpUserId, $v->sum('profit'), $setDate);
					$tmpData['totalProfit'] = $v->sum('profit');
					$tmpData['sale_group_name'] = $v->max('sale_group_name');
					$tmpData['user_name'] = $v->max('user_name');
					$tmpData['set_date'] = substr($setDate, 0, 7);
					$tmpData['erp_user_id'] = $erpUserId;
					return $tmpData;
				})->values();
				$progressDatas = $progressDatas->concat($items);
			});
			
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
			$groupProfitDatas = collect([]);
			collect($tmpGroups)->map(function ($items, $k) use (&$groupProfitDatas, $erpReturnData) {
				$items = collect($items)->map(function ($item, $k) use ($erpReturnData) {
					$item['profit'] = round($erpReturnData->where('sale_group_id', $item['id'])->where('set_date', $item['set_date'])->sum('profit'));
					$item['percentage'] = ($item['profit'] == 0 || $item['boundary'] == 0) ? 0 : round($item['profit'] / $item['boundary'] * 100);
					$item['set_date'] = substr($item['set_date'], 0, 7);
					return $item;
				});
				$groupProfitDatas = $groupProfitDatas->concat($items);
			});
			$erpReturnData = $erpReturnData->map(function ($v, $k) {
				$v['set_date'] = substr($v['set_date'], 0, 7);
				return $v;
			});
			
			return [$erpReturnData->toArray(),  $progressDatas->toArray(), $groupProfitDatas->toArray()];
		}
		
	}
	
	

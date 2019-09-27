<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-20
	 * Time: 10:15
	 */
	
	namespace App\Http\Controllers\Bonus;
	
	use App\FinancialList;
	use App\Http\Controllers\ApiController;
	use App\Http\Controllers\Auth\Permission;
	use App\Http\Controllers\BaseController;
	use App\Http\Controllers\Financial\FinancialListController;
	use App\Http\Controllers\Financial\ProvideController;
	use App\Http\Controllers\FinancialController;
	use App\Bonus;
	use App\BonusLevels;
	use App\SaleGroups;
	use App\User;
	use DateTime;
	use Gate;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Artisan;
	use Illuminate\Support\Facades\Input;
	use Auth;
	use App\Http\Controllers\UserController;
	
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
		
			//$yearMonth = '201907';
			//$yearMonthDay ='2019-07-01';
			//get financialData
			//$erpReturnData = $this->getFinancialData($erpUserId, $yearMonthDay, $yearMonth);
			
			
			//
			//$dateStart =  $date->format('Y-m-01');
			//$dateEnd = $date->format('Y-m-01');
			////$userIds = collect($userList)->pluck('erp_user_id')->toArray();
			//$request = new Request(['startDate' => $dateStart,'endDate'=>$dateEnd,'saleGroupIds' => [1,2,3,4],'userIds'=>null]);
			//$this->getAjaxData($request);
			
			////
			//$erpReturnData = $this->getFinancialData($userIds, $dateStart,$dateEnd);
			//$progressDatas = collect([]);
			//collect($erpReturnData)->groupBy(['set_date','erp_user_id'])->map(function($items,$setDate) use(&$progressDatas){
			//	$items = $items->map(function($v,$erpUserId) use($setDate){
			//		$tmpData = $this->getUserBonus($erpUserId, $v->sum('profit'), $setDate);
			//		$tmpData['sale_group_name'] = $v->max('sale_group_name');
			//		$tmpData['user_name'] = $v->max('user_name');
			//		$tmpData['set_date'] = substr($setDate,0,7);
			//		return $tmpData;
			//	})->values();
			//	$progressDatas = $progressDatas->concat($items);
			//});
			//dd($progressDatas);
			//
			//$totalAmounts = [
			// 'income' => [],
			//	'cost' => [],
			//	'profit' => []
			//];
			//$labels = [];
			//collect($erpReturnData)->groupBy('set_date')->map(function($v,$k) use(&$totalAmounts,&$labels){
			//	$tmpDate = new DateTime($k);
			//	$labels[] = $tmpDate->format('Ym');
			//	$totalAmounts['income'][] = round($v->sum('income'));
			//	$totalAmounts['cost'][] = round($v->sum('cost'));
			//	$totalAmounts['profit'][] = round($v->sum('profit'));
			//});
			//$chartDataBar = [
			// [
			//	'data' => $totalAmounts['income'],
			// ],
			// [
			//	'data' => $totalAmounts['cost'],
			// ],
			// [
			//	'data' => $totalAmounts['profit'],
			// ],
			//];
			
			
			
//			// loop calculation total Amount
//			list($totalIncome, $totalCost, $totalProfit, $erpReturnData) = $this->calculationTotal($erpReturnData);
//
//			// getUserBonus set output Data
//			$boxData = $this->getUserBonus($erpUserId, $totalProfit, $dateStart);
//
//			$tmpCollect = collect($erpReturnData);
//			$chartData = [
//			 [ 'data' => [
//				$tmpCollect->where('status','=',0 )->sum('income'),
//				$tmpCollect->where('status','>=',1 )->sum('income'),
//				0,
//				0
//			 ]],
//				//[ 'data' => [
//				//	 0,
//				//	 0,
//				//	 1,
//				//	 1]]
//			];
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
			//$progressData = [
			//	['set_date' => '2019-09',
			//		'user_name' => 'test',
			//		'progress' => 100,
			//	 'bonus_rate' => 5,
			//	 'bonus_total' => 100,
			//	 'sale_group_name' => "測試組"
			//	],
			// ['set_date' => '2019-10',
			//	'user_name' => 'test',
			//	'progress' => 70,
			//  'bonus_rate' => 5,
			//  'bonus_total' => 100,
			//  'sale_group_name' => "測試組"
			// ],
			// ['set_date' => '2019-11',
			//	'user_name' => 'test',
			//	'progress' => 50,
			//  'bonus_rate' => 5,
			//  'bonus_total' => 100,
			//  'sale_group_name' => "測試組"
			// ],
			// ['set_date' => '2019-09',
			//	'user_name' => 'test',
			//	'progress' => 30,
			//  'bonus_rate' => 5,
			//  'bonus_total' => 100,
			//  'sale_group_name' => "測試組"
			// ],
			// ['set_date' => '2019-09',
			//	'user_name' => 'test',
			//	'progress' => -55,
			//  'bonus_rate' => 0,
			//  'bonus_total' => 0,
			//  'sale_group_name' => "測試組"
			// ],
			//];

//
//			$dataTable = [
//			 'rowTitle' => [
//			  'year_month' => '月份',
//			  'campaign_name' => '名稱',
//			  'media_channel_name' =>	'媒體',
//			  'sell_type_name' =>	'類型' ,
//			  'currency_id' => '原幣別',
//			  'organization' => '公司',
//			  'income' =>	'收入' ,
//			  'cost' =>	'成本' ,
//			  'profit'	=> '毛利',
//			  'paymentStatus' => '收款狀態',
//			  'bonusStatus'	=> '發放狀態',
//			 ],
//				'data' => $erpReturnData
//			];
//			$userData = [
//			 'uId' => $erpUserId,
//			 'name' => session('users')[$erpUserId]['name'],
//			 'title' => session('users')[$erpUserId]['department_name'],
//			];
			
			return view('bonus.review.view',[
				'data' => $this->resources,
				//'userData' => $userData,
				//'dataTable' => $dataTable,
				'chartData' => $chartData,
				'chartDataBar' => $chartDataBar,
				'chartDataBarLabels' => $labels,
				'bonusData' => [],
				'bonusColumns' => $bonusColumns,
				'progressDatas' => [],
				'progressColumns' => $progressColumns,
				'saleGroups' => $saleGroups,
				'userList' => $userList
			]
			);
		}
		
		public function getAjaxData (Request $request)
		{
			$dateStart = $request->startDate;
			$dateEnd = $request->endDate;
			$saleGroupIds = $request->saleGroupIds;
			$userIds = $request->userIds;
			
			if(!empty($userIds)){
				$userIds = User::whereIn('id',$userIds)->get()->pluck('erp_user_id')->toArray();
			}
			
			if($saleGroupIds && empty($userIds)){
				$userIds = SaleGroups::with('groupsUsers')->whereIn('id', $saleGroupIds)->get()->map(function ($v, $k) {
					return $v->groupsUsers->pluck('erp_user_id');
				})->flatten()->unique()->values()->toArray();
			}
			
			$erpReturnData = collect($this->getFinancialData($userIds,$dateStart,$dateEnd))->map(function($v,$k){
			 return $v;
			});
			
			/*$chartMoneyStatus*/
			$chartMoneyStatus = [
			 'unpaid' => round($erpReturnData->where('status','=',0 )->sum('income')),
			 'paid' => round($erpReturnData->where('status','>=',1 )->sum('income'))
			];
			
			/*$chartFinancialBar*/
			$chartFinancialBar = [
			 'totalIncome' => [],
			 'totalCost' => [],
			 'totalProfit' => []
			];
			$erpReturnData->groupBy('set_date')->map(function($v,$k) use(&$chartFinancialBar){
				$tmpDate = new DateTime($k);
				$chartFinancialBar['labels'][] = $tmpDate->format('Ym');
				$chartFinancialBar['totalIncome'][] = round($v->sum('income'));
				$chartFinancialBar['totalCost'][] = round($v->sum('cost'));
				$chartFinancialBar['totalProfit'][] = round($v->sum('profit'));
			});
			/*progressDatas*/
			$progressDatas = collect([]);
			$erpReturnData->groupBy(['set_date','erp_user_id'])->map(function($items,$setDate) use(&$progressDatas){
				
				$items = $items->map(function($v,$erpUserId) use($setDate){
					$tmpData = $this->getUserBonus($erpUserId, $v->sum('profit'), $setDate);
					$tmpData['totalProfit'] = $v->sum('profit');
					$tmpData['sale_group_name'] = $v->max('sale_group_name');
					$tmpData['user_name'] = $v->max('user_name');
					$tmpData['set_date'] = substr($setDate,0,7);
					return $tmpData;
				})->values();
				$progressDatas = $progressDatas->concat($items);
			});
			
			$erpReturnData = $erpReturnData->map(function($v,$k){
				$v['set_date'] = substr($v['set_date'],0,7);
			 return $v;
			});
			
			echo json_encode(["bonus_list" => $erpReturnData,"chart_money_status" => $chartMoneyStatus,"chart_financial_bar" =>$chartFinancialBar,'progress_list'=>$progressDatas]);
		}
		/*watset*/
		public function getdata ()
		{
//			$dataTable = [
//				 ['yearMonth' => '201906',
//				 'campaignName' => '測試1',
//				 'mediaChannelName' => 'fb',
//				 'sellType' => 'CPC',
//				 'income' => 100000,
//				 'cost' => 50000,
//				 'profit' => 50000,
//				 'paymentStatus' => 0,
//				 'bonusStatus' => 0,]
//			];
			
			$uId = Input::get('uId');
			$inputDate = str_replace('/', '', Input::get('dateYearMonth'));
			$inputDate .= '01';
			$date = new \DateTime($inputDate);
			$yearMonthDay = $date->format('Y-m-01');
			
			//get financialData
			$erpReturnData = $this->getFinancialData($uId, $yearMonthDay);
			
			// loop calculation total Amount
			list($totalIncome, $totalCost, $totalProfit, $erpReturnData) = $this->calculationTotal($erpReturnData);
			
			// getUserBonus set output Data
			$boxData = $this->getUserBonus($uId, $totalProfit, $yearMonthDay);
			
			$tmpCollect = collect($erpReturnData);
			
			$chartMoneyStatus = [
			 'unpaid' => $tmpCollect->where('status','=',0 )->sum('income'),
				'paid' =>$tmpCollect->where('status','>=',1 )->sum('income')
				 ];
			
			$chartDataBar = [
			 'totalIncome' => $totalIncome,
				'totalCost' => $totalCost,
				'totalProfit' => $totalProfit
			];
			
			echo json_encode([
			 'erpReturnData' => $erpReturnData,
			 'chartDataBar' => $chartDataBar,
				'boxData' => $boxData,
				'chartMoneyStatus' => $chartMoneyStatus,
			 ]);
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
		
	}
	
	

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
	use App\Http\Controllers\FinancialController;
	use App\Bonus;
	use App\BonusLevels;
	use DateTime;
	use Gate;
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
		
		public function view($id = null)
		{
			$id = $id ?? session('userData')['id'];
			
			//return error
			if(isset(session('users')[$id]) == false){
				abort(404);
			}
			
			$loginUserId = session('userData')['id'];
			//check permission
			$permission = new Permission();
			$permission->permissionCheck($id,$loginUserId);
			
			$date = new \DateTime();
			$yearMonth = $date->format('Ym');
			$yearMonthDay = $date->format('Y-m-01');
			//$yearMonth = '201907';
			//$yearMonthDay ='2019-07-01';
			//get financialData
			$erpReturnData = $this->getFinancialData($id, $yearMonthDay, $yearMonth);
			
			// loop calculation total Amount
			list($totalIncome, $totalCost, $totalProfit, $erpReturnData) = $this->calculationTotal($erpReturnData);
			
			// getUserBonus set output Data
			$boxData = $this->getUserBonus($id, $totalProfit, $yearMonthDay);
			
			$tmpCollect = collect($erpReturnData);
			$chartData = [
			  [ 'data' => [
			   $tmpCollect->where('status','=',0 )->sum('income'),
			   $tmpCollect->where('status','>=',1 )->sum('income'),
				   0,
				   0
			  ]],
//			 [ 'data' => [
//					 0,
//					 0,
//					 1,
//					 1]]
			];
			
			$chartDataBar = [
				[
				 'data' => [$totalIncome],
				],
			  [
				 'data' => [$totalCost],
			  ],
			  [
				 'data' => [$totalProfit],
			  ],
			];
			
			$dataTable = [
			 'rowTitle' => [
			  'year_month' => '月份',
			  'campaign_name' => '名稱',
			  'media_channel_name' =>	'媒體',
			  'sell_type_name' =>	'類型' ,
			  'currency_id' => '原幣別',
			  'organization' => '公司',
			  'income' =>	'收入' ,
			  'cost' =>	'成本' ,
			  'profit'	=> '毛利',
			  'paymentStatus' => '收款狀態',
			  'bonusStatus'	=> '發放狀態',
			 ],
				'data' => $erpReturnData
			];
			$userData = [
			 'uId' => $id,
			 'name' => session('users')[$id]['name'],
			 'title' => session('users')[$id]['department_name'],
			];
			
			return view('bonus.review.view',[
				'data' => $this->resources,
				'userData' => $userData,
				'dataTable' => $dataTable,
				'chartData' => $chartData,
				'chartDataBar' => $chartDataBar,
				 'boxData' => $boxData,
			]
			);
		}

		
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
			$yearMonth = $date->format('Ym');
			$yearMonthDay = $date->format('Y-m-01');
			
			//get financialData
			$erpReturnData = $this->getFinancialData($uId, $yearMonthDay, $yearMonth);
			
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
				foreach ($erpReturnData as $key => $items) {
					$items = gettype($items) == 'object' ? get_object_vars($items) : $items;
					
					if ($items['income'] == 0 && $items['cost'] == 0) {
						unset($erpReturnData[$key]);
						continue;
					}
					
					$totalIncome += $items['income'];
					$totalCost += $items['cost'];
					
					$totalProfit += $items['profit'];
					
					if($items['organization'] == 'hk'){
						$erpReturnData[$key]['profit'] = sprintf('<p style="color:red">%d</p>',$items['profit']);
					}
					
					$erpReturnData[$key]['campaign_name'] = sprintf('<a href="http://%s/jsadwaysN2/campaign_view.php?id=%d" target="_blank">%s</a>',env('ERP_URL'),$items['cam_id'],$items['campaign_name']);
					
					$erpReturnData[$key]['paymentStatus'] = $erpReturnData[$key]['receipt']['created_at'] ?? 'no';
					$erpReturnData[$key]['bonusStatus'] = $erpReturnData[$key]['provide']['created_at'] ??'no';
					
				}
				sort($erpReturnData);
			}
			return array($totalIncome, $totalCost, $totalProfit, $erpReturnData);
		}
		
		/**
		 * @param $uId
		 * @param $totalProfit
		 * @param string $yearMonthDay
		 * @return array
		 */
		public function getUserBonus ($uId, $totalProfit, string $yearMonthDay): array
		{
			// getUserBonus
			$bonus = new Bonus();
			$returnBonusData = $bonus->getUserBonus($uId, $totalProfit, $yearMonthDay);
			
			// set output Data
			$boxData = [
			 'profit' => $returnBonusData['estimateBonus'],
			 'bonus_rate' => isset($returnBonusData['reachLevle']['bonus_rate']) ? $returnBonusData['reachLevle']['bonus_rate'] : 0,
			 'bonus_next_amount' => isset($returnBonusData['nextLevel']['bonus_next_amount']) ? $returnBonusData['nextLevel']['bonus_next_amount'] : 0,
			 'bonus_next_percentage' => isset($returnBonusData['nextLevel']['bonus_next_percentage']) ? $returnBonusData['nextLevel']['bonus_next_percentage'] : 0,
			 'bonus_direct' => $returnBonusData['bonusDirect']
			];
			return $boxData;
		}
		
		/**
		 * @param $id
		 * @param string $yearMonthDay
		 * @param string $yearMonth
		 * @return bool|mixed|string
		 */
		public function getFinancialData ($id, string $yearMonthDay, string $yearMonth)
		{
			//確認是否已有資料 反之call API
			$financialList = FinancialList::where(['erp_user_id' => $id, 'set_date' => $yearMonthDay])->with('receipt')->with('provide')->get();
			
			$financial = new FinancialController();
			if ($financialList->count() == 0) {
				// get api data
				$erpReturnData = collect($financial->getErpMemberFinancial([$id], $yearMonth));
				
				$erpReturnData = $erpReturnData->map(function ($v) use($financial) {
					return $financial->exchangeMoney($v);
				});
			} else {
				$erpReturnData = $financialList->map(function ($v) use($financial){
					return $financial->exchangeMoney($v->revertKeyChange()->revertValueChange());
				});
			}
			
			return $erpReturnData->toArray();
		}
	}
	
	

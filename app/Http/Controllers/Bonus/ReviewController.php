<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-20
	 * Time: 10:15
	 */
	
	namespace App\Http\Controllers\Bonus;
	
	
	use App\Http\Controllers\ApiController;
	use App\Http\Controllers\BaseController;
	use App\Http\Controllers\FinancialController;
	use App\Bonus;
	use App\BonusLevels;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Input;
	
	class ReviewController extends BaseController
	{

		
		public function __construct ()
		{
			//
			//			<!-- DataTables -->
			$this->resources['cssPath'][] = '/adminLte_componets/datatables.net-bs/css/dataTables.bootstrap.min.css';
			//		<!-- daterange picker -->
			$this->resources['cssPath'][] = '/adminLte_componets/bootstrap-daterangepicker/daterangepicker.css';
			//		<!-- bootstrap datepicker -->
			$this->resources['cssPath'][] = '/adminLte_componets/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css';
			//			<!-- iCheck for checkboxes and radio inputs -->
			$this->resources['cssPath'][] = '/adminLte_componets/plugins/iCheck/all.css';
			//			<!-- Bootstrap Color Picker -->
			$this->resources['cssPath'][] = '/adminLte_componets/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css';
			//			<!-- Bootstrap time Picker -->
			$this->resources['cssPath'][] = '/adminLte_componets/plugins/timepicker/bootstrap-timepicker.min.css';
			//			<!-- Select2 -->
			$this->resources['cssPath'][] = '/adminLte_componets/select2/dist/css/select2.min.css';
			
			$this->resources['jsPath'][] = '/adminLte_componets/datatables.net/js/jquery.dataTables.min.js';
			$this->resources['jsPath'][] = '/adminLte_componets/datatables.net-bs/js/dataTables.bootstrap.min.js';
			$this->resources['jsPath'][] = '/adminLte_componets/select2/dist/js/select2.full.min.js';
			$this->resources['jsPath'][] = '/adminLte_componets/plugins/input-mask/jquery.inputmask.js';
			$this->resources['jsPath'][] = '/adminLte_componets/plugins/input-mask/jquery.inputmask.extensions.js';
			$this->resources['jsPath'][] = '/adminLte_componets/bootstrap-daterangepicker/daterangepicker.js';
			$this->resources['jsPath'][] = '/adminLte_componets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js';
			$this->resources['jsPath'][] = '/adminLte_componets/bootstrap-datepicker/js/locales/bootstrap-datepicker.zh-TW.js';
			$this->resources['jsPath'][] = '/adminLte_componets/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js';
			$this->resources['jsPath'][] = '/adminLte_componets/plugins/timepicker/bootstrap-timepicker.min.js';
			$this->resources['jsPath'][] = '/adminLte_componets/plugins/iCheck/icheck.min.js';
//			$this->resources['jsPath'][] = '/adminLte_componets/chart.js/Chart.js';
		}
		
		public function list ()
		{
			$listdata = session('users');
			sort($listdata);
			return view('bonus.review.list',['data' => $this->resources,'row' => $listdata]);
		}
		
		public function view($id)
		{
			
			//return error
			if(isset(session('users')[$id]) == false){
				
				$message= [
				 'status' => 0,
				 'status_string' => '',
				 'message' => ''
				];
				$message['status_string'] = 'Error';
				$message['message'] = '查無使用者';
				
				return view('handle',['message'=>$message,'data' => $this->resources,'returnUrl' => Route('index')]);
			}
			
			// require css
//			$this->resources['cssPath'][] = '/css/bonusReviewView.css';
			
			// get api data
			$bonus = new Bonus();
			$date = new \DateTime();
			$yearMonth = $date->format('Ym');
			$financial = new FinancialController();
			$erpReturnData = $financial->getErpMemberFinancial([$id],$yearMonth);
			
			// loop calculation total Amount
			$totalIncome = 0;
			$totalCost = 0;
			$totalProfit = 0;
			
			foreach($erpReturnData as $key => $items){
				if($items['income'] == 0 && $items['cost'] == 0){
					unset($erpReturnData[$key]);
					continue;
				}
				$totalIncome += $items['income'];
				$totalCost += $items['cost'];
				$totalProfit += $items['profit'];
				
				$erpReturnData[$key]['paymentStatus'] = 'no';
				$erpReturnData[$key]['bonusStatus'] = 'no';
			}
			sort($erpReturnData);
			// getUserBonus
			
			$dateYearMonthDay = $date->format('Y-m-01');
			
			$returnBonusData = $bonus->getUserBonus($id, $totalProfit, $dateYearMonthDay);
			
			// set output Data
			$userData = [
			 'uId' => $id,
			 'name' => session('users')[$id]['name'],
			 'title' => session('users')[$id]['department_name'],
			];
			
			$boxData = [
			 'profit' => $returnBonusData['estimateBonus'],
				'bonus_rate' => isset($returnBonusData['reachLevle']['bonus_rate']) ? $returnBonusData['reachLevle']['bonus_rate'] : 0,
				'bonus_next_amount' => isset($returnBonusData['nextLevel']['bonus_next_amount']) ? $returnBonusData['nextLevel']['bonus_next_amount'] : 0,
				'bonus_next_percentage' => isset($returnBonusData['nextLevel']['bonus_next_percentage'])?  $returnBonusData['nextLevel']['bonus_next_percentage'] : 0,
			  'bonus_direct' => $returnBonusData['bonusDirect']
			];
			
			$chartData = [
			  [ 'data' => [
				   1,
				   1,
				   0,
				   0
			  ]],
			 [ 'data' => [
					 0,
					 0,
					 1,
					 1]]
			];
			
			$chartDataBar = [
				[
				 'data' => [$totalIncome],
				],
			  [
				 'data' => ['-'.$totalCost],
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
			  'income' =>	'收入' ,
			  'cost' =>	'成本' ,
			  'profit'	=> '毛利',
			  'paymentStatus' => '收款狀態',
			  'bonusStatus'	=> '發放狀態',
			 ],
				'data' => $erpReturnData
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
			// get api data
			$financial = new FinancialController();
			$erpReturnData = $financial->getErpMemberFinancial([$uId],$yearMonth);
			
			// loop calculation total Amount
			$totalIncome = 0;
			$totalCost = 0;
			$totalProfit = 0;
			
			foreach($erpReturnData as $key => $items){
				if($items['income'] == 0 && $items['cost'] == 0){
					unset($erpReturnData[$key]);
					continue;
				}
				$totalIncome += $items['income'];
				$totalCost += $items['cost'];
				$totalProfit += $items['profit'];
				
				$erpReturnData[$key]['paymentStatus'] = 'no';
				$erpReturnData[$key]['bonusStatus'] = 'no';
			}
			sort($erpReturnData);
			// getUserBonus
			$bonus = new Bonus();
			
			$returnBonusData = $bonus->getUserBonus($uId, $totalProfit, $yearMonthDay);
			
			
			// set output Data
			
			$boxData = [
			 'profit' => $returnBonusData['estimateBonus'],
			 'bonus_rate' => isset($returnBonusData['reachLevle']['bonus_rate']) ? $returnBonusData['reachLevle']['bonus_rate'] : 0,
			 'bonus_next_amount' => isset($returnBonusData['nextLevel']['bonus_next_amount']) ? $returnBonusData['nextLevel']['bonus_next_amount'] : 0,
			 'bonus_next_percentage' => isset($returnBonusData['nextLevel']['bonus_next_percentage'])?  $returnBonusData['nextLevel']['bonus_next_percentage'] : 0,
			 'bonus_direct' => $returnBonusData['bonusDirect']
			];
			
//			$chartData = [
//			 [ 'data' => [
//				1,
//				1,
//				0,
//				0
//			 ]],
//			 [ 'data' => [
//				0,
//				0,
//				1,
//				1]]
//			];
			
			$chartDataBar = [
			 'totalIncome' => $totalIncome,
				'totalCost' => $totalCost,
				'totalProfit' => $totalProfit
			];
			
			echo json_encode([
			 'erpReturnData' => $erpReturnData,
			 'chartDataBar' => $chartDataBar,
				'boxData' => $boxData
			 ]);
		}
	}
	
	

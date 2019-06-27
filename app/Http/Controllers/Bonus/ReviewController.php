<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-20
	 * Time: 10:15
	 */
	
	namespace App\Http\Controllers\Bonus;
	
	
	use App\Http\Controllers\BaseController;
	
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
			$listdata = [
			 [
				'id' => 1,
				'name' => 'Trident',
				'depatment' => 'Internet Explorer 4.0',
			 ],
			 [
				'id' => 2,
				'name' => 'Trident',
				'depatment' => 'Internet Explorer 4.0',
			 ],
			 [
				'id' => 3,
				'name' => 'Trident',
				'depatment' => 'Internet Explorer 4.0',
			 ],
			];
			return view('bonus.review.list',['data' => $this->resources,'row' => $listdata]);
		}
		
		public function view($id)
		{
			
			$this->resources['cssPath'][] = '/css/bounsReviewView.css';
			
			$userData = [
			 'name' => 'Toast',
			 'title' => '業務',
			 'lastMonthProfit' => 1000000,
			 'thisMonthProfit' => 900000,
			 'historyMonthProfit' => 1000000
			];
			$chartData = [
			  [ 'data' => [
				   1000,
				   500,
				   0,
				   0
			  ]],
			 [ 'data' => [
					 0,
					 0,
					 1000,
					 500]]
			];
			
			$chartDataBar = [
				[
				 'data' => [1000],
				],
			  [
				 'data' => [-2000],
			  ],
			  [
				 'data' => [-1000],
			  ],
			];
			
			$dataTable = [
			 'rowTitle' => [
			  'yearMonth' => '月份',
			  'campaignName' => '名稱',
			  'mediaChannelName' =>	'媒體',
			  'sellType' =>	'類型' ,
			  'income' =>	'收入' ,
			  'cost' =>	'成本' ,
			  'profit'	=> '毛利',
			  'paymentStatus' => '收款狀態',
			  'bonusStatus'	=> '發放狀態',
			 ],
				'data' => [
				 [
					'yearMonth' => '201906',
					'campaignName' => '測試1',
					'mediaChannelName' => 'fb',
					'sellType' => 'CPC',
					'income' => 100000,
					'cost' => 50000,
					'profit' => 50000,
					'paymentStatus' => '已收款',
					'bonusStatus' => '已收款',
				 ],[
					'yearMonth' => '201906',
					'campaignName' => '測試1',
					'mediaChannelName' => 'fb',
					'sellType' => 'CPC',
					'income' => 100000,
					'cost' => 50000,
					'profit' => 50000,
					'paymentStatus' => '未收款',
					'bonusStatus' => 0,
				 ]
				]
			];
			
			return view('bonus.review.view',[
			 'data' => $this->resources,
				'userData' => $userData,
			 'dataTable' => $dataTable,
				 'chartData' => $chartData,
				 'chartDataBar' => $chartDataBar
			]
			);
		}
		
		public function getdata ()
		{
			$dataTable = [
				 ['yearMonth' => '201906',
				 'campaignName' => '測試1',
				 'mediaChannelName' => 'fb',
				 'sellType' => 'CPC',
				 'income' => 100000,
				 'cost' => 50000,
				 'profit' => 50000,
				 'paymentStatus' => 0,
				 'bonusStatus' => 0,]
			];
			echo json_encode($dataTable);
		}
	}

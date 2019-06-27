<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-18
	 * Time: 14:17
	 */
	
	namespace App\Http\Controllers\Bonus;
	
	use App\Http\Controllers\BaseController;
	
	class SettingController extends BaseController
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
			$this->resources['jsPath'][] = '/adminLte_componets/fastclick/lib/fastclick.js';
			
			$this->resources['jsPath'][] = '/adminLte_componets/select2/dist/js/select2.full.min.js';
			$this->resources['jsPath'][] = '/adminLte_componets/plugins/input-mask/jquery.inputmask.js';
			$this->resources['jsPath'][] = '/adminLte_componets/plugins/input-mask/jquery.inputmask.extensions.js';
			
			$this->resources['jsPath'][] = '/adminLte_componets/moment/min/moment.min.js';
			$this->resources['jsPath'][] = '/adminLte_componets/bootstrap-daterangepicker/daterangepicker.js';
			$this->resources['jsPath'][] = '/adminLte_componets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js';
			$this->resources['jsPath'][] = '/adminLte_componets/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js';
			$this->resources['jsPath'][] = '/adminLte_componets/plugins/timepicker/bootstrap-timepicker.min.js';
			$this->resources['jsPath'][] = '/adminLte_componets/plugins/iCheck/icheck.min.js';
		}
		
		public function add ()
		{
			$addUserLists = [
			 ['name' => 'test1', 'id'=> 1],
			 ['name' => 'test2', 'id'=> 2],
			];
			return view('bonus.setting.add',['data' => $this->resources,'addUserLists' => $addUserLists]);
		}
		public function list ()
		{
			$listdata = [
			 [
			  'id' => 1,
				'name' => 'Trident',
				'depatment' => 'Internet Explorer 4.0',
				'boundary' => 300000
			 ],
			[
			 'id' => 2,
				'name' => 'Trident',
				'depatment' => 'Internet Explorer 4.0',
				'boundary' => 200000
			 ],
			[
			 'id' => 3,
				'name' => 'Trident',
				'depatment' => 'Internet Explorer 4.0',
				'boundary' => 100000
			 ],
			];
			
			return view('bonus.setting.list',['data' => $this->resources,'row' => $listdata]);
		}
		
		public function edit($id)
		{
			$editData = [
			 'boundary' => 4000000,
				'rateData' => [
				 ['id'=>1,'achievingRate' => '10', 'bounsRate' => '10'],
				 ['id'=>2,'achievingRate' => '10', 'bounsRate' => '10']
				]
			];
			
			$userData = [
			 'name' => '小米',
			  'title' => '業務總監',
				'lastMonthProfit' => 1000000,
				'thisMonthProfit' => 900000,
				'historyMonthProfit' => 1000000
			];
			
			$userBonusHistory = [
			 [
			  'dateMonth' => '2019-05',
			  'boundary' => '1000000',
			  'rateData' => [
			   ['id'=>1,'achievingRate' => '10', 'bounsRate' => '10'],
			   ['id'=>2,'achievingRate' => '10', 'bounsRate' => '10']
			  ]
			 ],[
				'dateMonth' => '2019-04',
				'boundary' => '1000000',
				'rateData' => [
				 ['id'=>1,'achievingRate' => '10', 'bounsRate' => '10'],
				 ['id'=>2,'achievingRate' => '10', 'bounsRate' => '10']
				]
			 ],[
				'dateMonth' => '2019-03',
				'boundary' => '1000000',
				'rateData' => [
				 ['id'=>1,'achievingRate' => '10', 'bounsRate' => '10'],
				 ['id'=>2,'achievingRate' => '10', 'bounsRate' => '10']
				]
			 ]
			];
			
			return view('bonus.setting.edit',['data' => $this->resources,'row'=>$editData,'userData' => $userData,'userBonusHistory' => $userBonusHistory ]);
		}
		
		public function view($id)
		{
			$userData = [
			 'name' => '小米',
			 'title' => '業務',
			 'lastMonthProfit' => 1000000,
			 'thisMonthProfit' => 900000,
			 'historyMonthProfit' => 1000000
			];
			
			$userBonusHistory = [
			 [
				'dateMonth' => '2019-05',
				'boundary' => '1000000',
				'rateData' => [
				 ['id'=>1,'achievingRate' => '10', 'bounsRate' => '10'],
				 ['id'=>2,'achievingRate' => '10', 'bounsRate' => '10']
				]
			 ],[
				'dateMonth' => '2019-04',
				'boundary' => '1000000',
				'rateData' => [
				 ['id'=>1,'achievingRate' => '10', 'bounsRate' => '10'],
				 ['id'=>2,'achievingRate' => '10', 'bounsRate' => '10']
				]
			 ],[
				'dateMonth' => '2019-03',
				'boundary' => '1000000',
				'rateData' => [
				 ['id'=>1,'achievingRate' => '10', 'bounsRate' => '10'],
				 ['id'=>2,'achievingRate' => '10', 'bounsRate' => '10']
				]
			 ]
			];
			
			return view('bonus.setting.view',['data' => $this->resources,'userData' => $userData,'userBonusHistory' => $userBonusHistory ]);
		}
		
		public function save ()
		{
			dd($_POST);
		}
	}

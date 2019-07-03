<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-18
	 * Time: 14:17
	 */
	
	namespace App\Http\Controllers\Bonus;
	
	use App\Http\Controllers\ApiController;
	use Illuminate\Contracts\Session\Session;
	use Illuminate\Http\Request;
	use App\Http\Controllers\BaseController;
	use App\Http\Controllers\UserController;
	use App\Bonus;
	use App\BonusLevels;
	use Illuminate\Support\Facades\Redirect;
	use Validator;
	use App\Exceptions\Handler;
	use Illuminate\Support\Arr;
	
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
			
			$userObj = new UserController();
			$userObj->getErpUser();
			session(['department' => $userObj->department]);
			session(['users' => $userObj->users]);
//			dd(session()->all());
//
		}
		
		public function add ()
		{
			$userObj = new UserController();
			$alreadySetUserIds = Bonus::select('user_id')->groupBy('user_id')->get()->toArray();
			$alreadySetUserIds = Arr::flatten($alreadySetUserIds);
			
			//for test data
//			$addUserLists = [
//			 ['name' => 'test1', 'id'=> 1],
//			 ['name' => 'test2', 'id'=> 2],
//			];
			$userObj->getErpUser();
			$addUserLists = $userObj->sortUserData('unsetOld');

			$newtmp =[];
			$another = [];
//			16,14,25,34,33,32 業務部門 id 排序一下
			foreach($addUserLists as $key => $item){
			 switch($key){
				 case 16:
					 $newtmp[0] = $item;
					 break;
				 case 14:
					 $newtmp[1] = $item;
					 break;
				 case 25:
					 $newtmp[2] = $item;
					 break;
				 case 34:
					 $newtmp[3] = $item;
					 break;
				 case 33:
					 $newtmp[4] = $item;
					 break;
				 case 32:
					 $newtmp[5] = $item;
					 break;
				 default:
					 $another[] = $item;
			 }
			}
			ksort($newtmp);
			$addUserLists = array_merge($newtmp,$another);
			
			return view('bonus.setting.add',['data' => $this->resources,'addUserLists' => $addUserLists,'alreadySetUserIds' => $alreadySetUserIds]);
		}
		
		public function list ()
		{
			$date = new \DateTime();
			
			$bonus = Bonus::where('set_date','=',$date->format('Y-m-01'))->get()->toArray();
	
			$listdata = $bonus;
			
//			$listdata = [
//			 [
//			  'id' => 1,
//				'name' => 'Trident',
//				'depatment' => 'Internet Explorer 4.0',
//				'boundary' => 300000
//			 ],
//			[
//			 'id' => 2,
//				'name' => 'Trident',
//				'depatment' => 'Internet Explorer 4.0',
//				'boundary' => 200000
//			 ],
//			[
//			 'id' => 3,
//				'name' => 'Trident',
//				'depatment' => 'Internet Explorer 4.0',
//				'boundary' => 100000
//			 ],
//			];
			return view('bonus.setting.list',['data' => $this->resources,'row' => $listdata]);
		}
		
		public function edit($id)
		{
			$bonus = Bonus::where('id',$id)->with('levels')->get()->toArray()[0];
			
			$userTmpData = session('users')[$bonus['user_id']];
			
			$editData = $bonus;
			
			$userData = [
			 'name' => $userTmpData['name'],
			  'title' => session('department')[$userTmpData['department_id']]['name'],
				'lastMonthProfit' => 0,
				'thisMonthProfit' => 0,
				'historyMonthProfit' => 0
			];
			
			$userBonusHistory = Bonus::where('user_id',$bonus['user_id'])->with('levels')->get()->toArray();
			
			$userBonusHistory = $userBonusHistory;
			
			
//			$userBonusHistory = [
//			 [
//			  'set_date' => '2019-05',
//			  'boundary' => '1000000',
//			  'levels' => [
//			   ['id'=>1,'achieving_rate' => '10', 'bouns_rate' => '10'],
//			   ['id'=>2,'achieving_rate' => '10', 'bouns_rate' => '10']
//			  ]
//			 ],[
//				'set_date' => '2019-04',
//				'boundary' => '1000000',
//				'levels' => [
//				 ['id'=>1,'achieving_rate' => '10', 'bouns_rate' => '10'],
//				 ['id'=>2,'achieving_rate' => '10', 'bouns_rate' => '10']
//				]
//			 ],[
//				'set_date' => '2019-03',
//				'boundary' => '1000000',
//				'levels' => [
//				 ['id'=>1,'achieving_rate' => '10', 'bouns_rate' => '10'],
//				 ['id'=>2,'achieving_rate' => '10', 'bouns_rate' => '10']
//				]
//			 ]
//			];
			
			return view('bonus.setting.edit',['data' => $this->resources,'row'=>$editData,'userData' => $userData,'userBonusHistory' => $userBonusHistory ]);
		}
		
		public function view($id)
		{
//			$userData = [
//			 'name' => '小米',
//			 'title' => '業務',
//			 'lastMonthProfit' => 1000000,
//			 'thisMonthProfit' => 900000,
//			 'historyMonthProfit' => 1000000
//			];

			$bonus = Bonus::where('user_id',$id)->with('levels')->get()->toArray();
			
			
			$userData = [
			 'uId' => $id,
			 'name' => session('users')[$id]['name'],
			 'title' => session('users')[$id]['department_name'],
			 'lastMonthProfit' => 0,
			 'thisMonthProfit' => 0,
			 'historyMonthProfit' => 0
			];
			
			$userBonusHistory = $bonus;
//			$userBonusHistory = [
//			 [
//				'set_date' => '2019-05',
//				'boundary' => '1000000',
//				'levels' => [
//				 ['id'=>1,'achieving_rate' => '10', 'bouns_rate' => '10'],
//				 ['id'=>2,'achieving_rate' => '10', 'bouns_rate' => '10']
//				]
//			 ],[
//				'set_date' => '2019-04',
//				'boundary' => '1000000',
//				'levels' => [
//				 ['id'=>1,'achieving_rate' => '10', 'bouns_rate' => '10'],
//				 ['id'=>2,'achieving_rate' => '10', 'bouns_rate' => '10']
//				]
//			 ],[
//				'set_date' => '2019-03',
//				'boundary' => '1000000',
//				'levels' => [
//				 ['id'=>1,'achieving_rate' => '10', 'bouns_rate' => '10'],
//				 ['id'=>2,'achieving_rate' => '10', 'bouns_rate' => '10']
//				]
//			 ]
//			];
			
			return view('bonus.setting.view',['data' => $this->resources,'userData' => $userData,'userBonusHistory' => $userBonusHistory ]);
		}
		
		public function save (Request $request)
		{
			
			$date = new \DateTime();
			$request->merge(['set_date' => $date->format('Y-m-01')]);
			$bonus = Bonus::create($request->all());
			if($request->bouns_levels){
				foreach( $request->bouns_levels as $item){
					$item['bonus_id'] = $bonus->id;
					BonusLevels::create($item);
				}
			}
			
			
			return redirect()->action('Bonus\SettingController@edit',['id'=>$bonus->id]);
			
		}
		
		public function update (Request $request)
		{
//			$validator = Validator::make($request->all(),[
//			 'bonus_id' =>'required',
//			]);
//
//			$message= [
//			 'status' => 0,
//			 'status_string' => '',
//			 'message' => ''
//			];
//
//			if($validator->fails()){
//				$error = $validator->errors()->toArray();
//				$error = reset($error);
//
//				$message['status_string'] = '驗證錯誤';
//				$message['message'] = $error[0];
//
//				return view('handle',['message'=>$message,'data' => $this->resources,'returnUrl' => Route('bonus.setting.list') ]);
//			}
			$bonus = Bonus::where('id',$request->bonus_id)->with('levels')->first();
			
			$date = new \DateTime();
			//新月份
			if($bonus->set_date != $date->format('Y-m-01')){
				$request->offsetUnset('bonus_id');
				$request->merge(['user_id' => $bonus->user_id]);
				return $this->save($request);
			}else{
				$bonus->boundary = $request->boundary;
				$bonus->save();
				BonusLevels::where('bonus_id',$request->bonus_id)->delete();
				if($request->bouns_levels){
					foreach( $request->bouns_levels as $item){
						$item['bonus_id'] = $bonus->id;
						BonusLevels::create($item);
					}
				}
				$message= [
				 'status' => 0,
				 'status_string' => '',
				 'message' => ''
				];
				$message['status_string'] = '更新完成';
				$message['message'] = '';
				
				return view('handle',['message'=>$message,'data' => $this->resources,'returnUrl' => Route('bonus.setting.edit',['id' => $bonus->id]) ]);
			}
			
		}
	}

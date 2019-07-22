<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-18
	 * Time: 14:17
	 */
	
	namespace App\Http\Controllers\Bonus;
	
	use App\Http\Controllers\ApiController;
	use App\Http\Controllers\Auth\Permission;
	use App\Http\Controllers\FinancialController;

	use Illuminate\Contracts\Auth\Access\Gate;
	use Illuminate\Contracts\Auth\Access\Gate as GateContract;
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
	use App\Role;
	use Auth;
	
	class SettingController extends BaseController
	{
		
		public function __construct ()
		{
			parent::__construct();
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
//				$role = new Role();
//			  $role->
//			$role = Role::first();
//			$permission = \App\Permission::first();
//			$role->givePermissionTo($permission);
			
//			Auth::loginUsingId(1, true);
//			app(\Illuminate\Contracts\Auth\Access\Gate::class)->abilities();
//			dd(Auth::user());

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
			$pageUid = $bonus['user_id'];
			
			$loginUserId = session('userData')['user']['id'];
			$message = $this->permissionCheck($pageUid,$loginUserId);
			
			if($message['status'] == 0){
				
				return view('handle',['message'=>$message,'data' => $this->resources,'returnUrl' => Route('index') ]);
				
			}
			
//			dd($bonus,$id);
			$userTmpData = session('users')[$bonus['user_id']];
			
			$editData = $bonus;
			$financial = new FinancialController();
			$getlatelyMonth = $financial->getUserLatelyProfit($bonus['user_id']);
			extract($getlatelyMonth);
			
			$userData = [
			 'name' => $userTmpData['name'],
			  'title' => session('department')[$userTmpData['department_id']]['name'],
				'lastMonthProfit' => $lastMonthProfit,
				'thisMonthProfit' => $thisMonthProfit,
				'historyMonthProfit' => $highestProfit
			];
			
			$userBonusHistory = Bonus::where('user_id',$bonus['user_id'])->orderBy('id','DESC')->with('levels')->get()->toArray();
			
			$userBonusHistory = $userBonusHistory;
			
//			$userBonusHistory = [
//			 [
//			  'set_date' => '2019-05',
//			  'boundary' => '1000000',
//			  'levels' => [
//			   ['id'=>1,'achieving_rate' => '10', 'bonus_rate' => '10'],
//			   ['id'=>2,'achieving_rate' => '10', 'bonus_rate' => '10']
//			  ]
//			 ],[
//				'set_date' => '2019-04',
//				'boundary' => '1000000',
//				'levels' => [
//				 ['id'=>1,'achieving_rate' => '10', 'bonus_rate' => '10'],
//				 ['id'=>2,'achieving_rate' => '10', 'bonus_rate' => '10']
//				]
//			 ],[
//				'set_date' => '2019-03',
//				'boundary' => '1000000',
//				'levels' => [
//				 ['id'=>1,'achieving_rate' => '10', 'bonus_rate' => '10'],
//				 ['id'=>2,'achieving_rate' => '10', 'bonus_rate' => '10']
//				]
//			 ]
//			];
			
			return view('bonus.setting.edit',['data' => $this->resources,'row'=>$editData,'userData' => $userData,'userBonusHistory' => $userBonusHistory ]);
		}
		
		public function view($id = null)
		{
			$id = $id ?? session('userData')['user']['id'];
//			$userData = [
//			 'name' => '小米',
//			 'title' => '業務',
//			 'lastMonthProfit' => 1000000,
//			 'thisMonthProfit' => 900000,
//			 'historyMonthProfit' => 1000000
//			];
			$loginUserId = session('userData')['user']['id'];
			$message = $this->permissionCheck($id,$loginUserId);
			
			if($message['status'] == 0){
				return view('handle',['message'=>$message,'data' => $this->resources,'returnUrl' => Route('index') ]);
			}
			
			$bonus = Bonus::where('user_id',$id)->with('levels')->get()->toArray();
			
			
			// 排程儲存
//			$bonus = Bonus::where('set_date','2019-07-01')->with('levels')->get()->toArray();
//			$tests = collect($bonus);
			
//				$importData = $this->importbonusSetting();
//				foreach($importData as $item){
//					$request = new Request($item);
//					$this->save($request,$request->set_date);
//				}
//				dd($importData);
			
//
//			foreach($tests  as $item){
//				$unset = ['created_at','updated_at','id','set_date'];
//				foreach($unset as $unt){
//					unset($item[$unt]);
//				}
//				$item['set_date'] = '2019-09-01';
//
//
//				$bonus = Bonus::create($item);
//				if($item['levels']){
//					foreach( $item['levels'] as $item){
//						$item['bonus_id'] = $bonus->id;
//						BonusLevels::create($item);
//					}
//				}
//			}
			
			
			
			if(empty(session('users')[$id])){
				abort(500,'訪問頁面資料錯誤請確認Url是否正確');
			}
			$financial = new FinancialController();
			$getlatelyMonth = $financial->getUserLatelyProfit($id);
			extract($getlatelyMonth);

			
			$userData = [
			 'uId' => $id,
			 'name' => session('users')[$id]['name'],
			 'title' => session('users')[$id]['department_name'],
			 'lastMonthProfit' => $thisMonthProfit,
			 'thisMonthProfit' => $lastMonthProfit,
			 'historyMonthProfit' => $highestProfit
			];
			
			$userBonusHistory = $bonus;
//			$userBonusHistory = [
//			 [
//				'set_date' => '2019-05',
//				'boundary' => '1000000',
//				'levels' => [
//				 ['id'=>1,'achieving_rate' => '10', 'bonus_rate' => '10'],
//				 ['id'=>2,'achieving_rate' => '10', 'bonus_rate' => '10']
//				]
//			 ],[
//				'set_date' => '2019-04',
//				'boundary' => '1000000',
//				'levels' => [
//				 ['id'=>1,'achieving_rate' => '10', 'bonus_rate' => '10'],
//				 ['id'=>2,'achieving_rate' => '10', 'bonus_rate' => '10']
//				]
//			 ],[
//				'set_date' => '2019-03',
//				'boundary' => '1000000',
//				'levels' => [
//				 ['id'=>1,'achieving_rate' => '10', 'bonus_rate' => '10'],
//				 ['id'=>2,'achieving_rate' => '10', 'bonus_rate' => '10']
//				]
//			 ]
//			];
			
			return view('bonus.setting.view',['data' => $this->resources,'userData' => $userData,'userBonusHistory' => $userBonusHistory ]);
		}
		
		public function save (Request $request,$setDate = null)
		{
			
			$date = new \DateTime();
			$request->merge(['set_date' => isset($setDate) ? $setDate :$date->format('Y-m-01') ]);
			
			if( !$this->checkbonusLevelsUni($request->bonus_levels) ){
				
				$message= [
				 'status' => 0,
				 'status_string' => '',
				 'message' => ''
				];
				
				$message['status_string'] = 'fail';
				$message['message'] = '資料有誤 達成比例不能重複';
				
				return view('handle',['message'=>$message,'data' => $this->resources,'returnUrl' => Route('bonus.setting.add') ]);
			}
			
			$bonus = Bonus::create($request->all());
			
			if($request->bonus_levels){
				foreach( $request->bonus_levels as $item){
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
				
				
				if( !$this->checkbonusLevelsUni($request->bonus_levels) ){
					
					$message= [
					 'status' => 0,
					 'status_string' => '',
					 'message' => ''
					];
					
					$message['status_string'] = 'fail';
					$message['message'] = '資料有誤 達成比例不能重複';
					
					return view('handle',['message'=>$message,'data' => $this->resources,'returnUrl' => Route('bonus.setting.edit',['id' => $bonus->id]) ]);
				}
				
				BonusLevels::where('bonus_id',$request->bonus_id)->delete();
				if($request->bonus_levels){
					foreach( $request->bonus_levels as $item){
						$item['bonus_id'] = $bonus->id;
						BonusLevels::create($item);
					}
				}
				
				$message= [
				 'status' => 0,
				 'status_string' => '',
				 'message' => ''
				];
				
				$message['status_string'] = 'success';
				$message['message'] = '更新成功';
				
				return view('handle',['message'=>$message,'data' => $this->resources,'returnUrl' => Route('bonus.setting.edit',['id' => $bonus->id]) ]);
			}
			
		}
		
		public function permissionCheck ($id,$uid)
		{
			$permission = new Permission();
			//check permission
			
			$message= [
			 'status' => 1,
			 'status_string' => '',
			 'message' => ''
			];
			
			if(!in_array($uid,$permission->role['admin']['ids']) && $id != $uid){
				$message['status'] = 0;
				$message['status_string'] = '沒有權限';
				$message['message'] = '已異常訪問紀錄'.session('userData')['user']['name'];
				return $message;
			};
			
			return $message;
		}
		
		public function checkbonusLevelsUni ($children)
		{
			$tmp = collect($children);
			return count($children) != count($tmp->keyBy('achieving_rate')) ? false : true ;
		}
		
		public function getUserLatelyProfit ($uid)
		{
			$lastMonth = new \DateTime('last day of last month');
			$lastMonth = $lastMonth->format('Ym');
			$thisMonth = new \DateTime();
			$thisMonth = $thisMonth->format('Ym');
			
			$financial = new FinancialController();
			$erpReturnData = $financial->getErpMemberFinancial([$uid],'all','all');
			$erpReturnData = collect($erpReturnData);
			
			$thisMonthProfit = $erpReturnData->filter(function($item) use($thisMonth) {
				return $item['year_month'] == $thisMonth;
			})->max('profit');
			$lastMonthProfit = $erpReturnData->filter(function($item) use($lastMonth) {
				return $item['year_month'] == $lastMonth;
			})->max('profit');
			$highestProfit =  $erpReturnData->max('profit');
			
			
			return [
			 'thisMonthProfit' => $thisMonthProfit,
				'lastMonthProfit' => $lastMonthProfit,
				'highestProfit' => $highestProfit
			 ];
		}
		
		public function importbonusSetting(){
			
			//			$importData = $this->importbonusSetting();
			//			foreach($importData as $item){
			//				$request = new Request($item);
			//				$this->save($request,$request->set_date);
			//			}
			//			dd($importData);
			
			//讀檔案
			$file = fopen(asset('storage/bonus.csv'), "r");
			$i=0;
			//確認奇怪字元
			$cheeckStr = [];
			//空白資料
			$emptyData = [];
			//output
			$objfileArr = [];
			while(! feof($file))
			{
				foreach(fgetcsv($file) as $item){
					// 轉碼 去空白
					//					$item = trim(mb_convert_encoding($item,'utf-8','big5'));
					//					$item = trim(mb_convert_encoding($item,'utf-8','auto'));
					// 驗證如有 無法辨識字元
					if(strpos($item,'?')){
						$cheeckStr[]=$i;
					};
					if(empty($item)){
						$emptyData[$i] = isset($objfileArr[$i]) ? $objfileArr[$i] : [];
						$emptyData[$i][] =  $item;
						unset($objfileArr[$i]);
					}else{
						$objfileArr[$i][] = $item;
					}
				}
				$i++;
			};
			
			$userData = collect(session('users'));
			//去標題
			unset($objfileArr[0]);
			$newtmp = [];
			//替換keyName 與 user id
			foreach($objfileArr as $key => $item){
				$newtmp[$key]['set_date'] = $item[0];
				$newtmp[$key]['user_id'] = $userData->where('account',$item[1])->max('id');
				$newtmp[$key]['boundary'] = $item[2];
				$childrenKey = 0 ;
				//分裝 levels children
				for( $i = 3; $i < count($item);$i++ ){
					$cloumStr = $i%2 == 1 ? 'achieving_rate' : 'bonus_rate';
					$newtmp[$key]['bonus_levels'][$childrenKey][$cloumStr] = $item[$i];
					if($i%2 == 0){
						$childrenKey++;
					}
				}
			}
			$setDateArr = ['2018-05-01','2018-06-01','2018-07-01','2018-08-01','2018-09-01','2018-10-01','2018-11-01','2018-12-01','2019-01-01','2019-02-01','2019-03-01','2019-04-01','2019-05-01','2019-06-01','2019-07-01'];
			$importDatas = $newtmp;
			foreach($importDatas as $importData){
				foreach($importData['bonus_levels'] as $key => $item){
						if($key <= 2 ){
							$bonusDirect = 0;
						}else if( $key == 3){
							$bonusDirect = 15000;
						}else{
							$bonusDirect = 20000;
						}
					$importData['bonus_levels'][$key]['bonus_direct'] = $bonusDirect;
				}
				foreach($setDateArr as $set_date){
					$request = new Request($importData);
					$this->save($request,$set_date);
				}
			}
			return $importData;
		}
	}

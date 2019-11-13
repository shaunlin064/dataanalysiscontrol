<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-18
	 * Time: 14:17
	 */
	
	namespace App\Http\Controllers\Bonus;
	
	use App\FinancialList;
	use App\Http\Controllers\Auth\Permission;
	use App\Http\Controllers\Financial\FinancialListController;
	use App\User;
	use Illuminate\Http\Request;
	use App\Http\Controllers\BaseController;
	use App\Http\Controllers\UserController;
	use App\Bonus;
	use App\BonusLevels;
	use Validator;
	use Illuminate\Support\Arr;
	use Auth;
	
	class SettingController extends BaseController
	{
		protected $policyModel;
		
		public function __construct () {
			
			parent::__construct();
			
			$this->policyModel = new Bonus();
		}
		
		public function add ()
		{
			$this->authorize('create',$this->policyModel);
			
			$userObj = new UserController();
            $userObj->getErpUser();
            $addUserLists = $userObj->sortUserData('unsetOld');
            
			$alreadySetUserIds = Bonus::groupBy('erp_user_id')->get()->pluck('erp_user_id');
   
			return view('bonus.setting.add',['data' => $this->resources,'addUserLists' => $addUserLists,'alreadySetUserIds' => $alreadySetUserIds]);
		}
		
		public function list ()
		{
			$this->authorize('create',$this->policyModel);
			
			$date = new \DateTime();
			
			$listdata = Bonus::where('set_date','=',$date->format('Y-m-01'))->get()->map(function($v){
				$v->name =  ucfirst($v->user->name);
				$v->sale_groups_name = $this->getUserGroups($v);
			 return $v;
			})->toArray();
			
			return view('bonus.setting.list',['data' => $this->resources,'listdata' => $listdata]);
		}
		
		public function edit(Bonus $bonus)
		{
			$this->authorize('create',$this->policyModel);
			
            $bonus->levels;
			
			$erpUserId = $bonus->erp_user_id;
			
			list($highestProfit,$thisMonthProfit,$lastMonthProfit) = $this->getUserLatelyProfit($erpUserId);
			
			$userData = [
			  'name' => $bonus->user->name,
			  'title' => $this->getUserGroups($bonus),
				'lastMonthProfit' => $lastMonthProfit,
				'thisMonthProfit' => $thisMonthProfit,
				'historyMonthProfit' => $highestProfit
			];
			
			$userBonusHistory = $this->getBonusHistory($erpUserId);
			
			return view('bonus.setting.edit',['data' => $this->resources,'row'=>$bonus,'userData' => $userData,'userBonusHistory' => $userBonusHistory ]);
		}
		
		public function view($uid = null)
		{
            $loginUserId = Auth::user()->erp_user_id;
			$uid = $uid ?? $loginUserId;
			
			abort_if(User::where('erp_user_id',$uid)->doesntExist(),404);
			
            //check permission
            $bonus = $this->policyModel->where('erp_user_id',$uid)->first();
            
//            $this->authorize('viewAny',$bonus ?? $this->policyModel);
   
			list($highestProfit,$thisMonthProfit,$lastMonthProfit) = $this->getUserLatelyProfit($uid);
			
			$userData = [
			 'uId' => $uid,
			 'name' => $bonus->user->name ?? ucfirst(auth()->user()->name),
			 'title' => $this->getUserGroups($bonus) ?? session('userData')['department'],
			 'lastMonthProfit' => $lastMonthProfit,
			 'thisMonthProfit' => $thisMonthProfit,
			 'historyMonthProfit' => $highestProfit
			];
			$userBonusHistory = $this->getBonusHistory($uid);
			
			return view('bonus.setting.view',['data' => $this->resources,'userData' => $userData,'userBonusHistory' => $userBonusHistory ]);
		}
		
		public function save (Request $request,$setDate = null)/**/
		{
			$this->authorize('create',$this->policyModel);
			
			$date = new \DateTime();
			$request->merge(['set_date' => isset($setDate) ? $setDate :$date->format('Y-m-01') ]);
			
			$bonus = Bonus::create($request->all());
   
			if($request->bonus_levels){
			    
                if( !$this->checkbonusLevelsUni($request->bonus_levels) ){
                    return $this->notUni($bonus);
                }
                
				foreach( $request->bonus_levels as $item){
					$item['bonus_id'] = $bonus->id;
					BonusLevels::create($item);
				}
			}
            
            return redirect()->route('bonus.setting.edit',['id'=>$bonus->id]);
		}
		
		public function update (Request $request)
		{
			$this->authorize('create',$this->policyModel);
			
			$bonus = Bonus::where('id',$request->bonus_id)->with('levels')->first();
			
			$date = new \DateTime();
			//新月份
   
			if($bonus->set_date != $date->format('Y-m-01')){
				$request->offsetUnset('bonus_id');
				$request->merge(['erp_user_id' => $bonus->erp_user_id]);
				
				$this->save($request);
    
			}else{
				$bonus->boundary = $request->boundary;
				$bonus->save();
    
				
				
				BonusLevels::where('bonus_id',$request->bonus_id)->delete();
				if($request->bonus_levels){
                    if( !$this->checkbonusLevelsUni($request->bonus_levels) ){
                        return $this->notUni($bonus);
                    }
                    
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
		
		public function checkbonusLevelsUni ($children)
		{
			$tmp = collect($children);
			return count($children) != count($tmp->keyBy('achieving_rate')) ? false : true ;
		}
		
		/**
		 * @param $uid
		 * @return mixed
		 */
		private function getBonusHistory ($erpUserId)
		{
			$userBonusHistory = Bonus::where('erp_user_id', $erpUserId)->orderBy('id', 'DESC')->with('levels')->get()->toArray();
			return $userBonusHistory;
		}
		
		/**
		 * @param $bonus
		 * @return array
		 */
		private function getUserLatelyProfit ($erpUserId): array
		{
			$financialListObj = new FinancialList();
			return $financialListObj->getUserLatelyProfit($erpUserId);
		}
		
		/**
		 * @param $bonus
		 * @return mixed
		 */
		private function getUserGroups ($bonus)
		{
		    if(empty($bonus)){
		        return null;
            }
			$groupNames = $bonus->saleGroups->map(function ($v) {
				return $v->saleGroups->name;
			})->implode(',');
			return $groupNames;
		}
        
        /**
         * @param $bonus
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        private function notUni ($bonus = null)
        {
            $message = [
                'status' => 0,
                'status_string' => '',
                'message' => ''
            ];
            
            $message['status_string'] = 'fail';
            $message['message'] = '資料有誤 達成比例不能重複';
            
            return view('handle', ['message' => $message, 'data' => $this->resources, 'returnUrl' => Route('bonus.setting.edit', ['id' => $bonus->id])]);
        }
    }

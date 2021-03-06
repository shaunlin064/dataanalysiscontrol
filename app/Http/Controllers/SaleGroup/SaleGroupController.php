<?php

namespace App\Http\Controllers\SaleGroup;

use App\Bonus;
use App\SaleGroups;
use App\SaleGroupsUsers;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;

class SaleGroupController extends BaseController
{
    //
    protected $policyModel;

    public function __construct () {

        parent::__construct();

        $this->policyModel = new SaleGroups();
    }

    public function list ()
    {
        $this->authorize('create',$this->policyModel);

        $saleGroups = SaleGroups::all();
        return view('saleGroup.setting.list',['data' => $this->resources,'row' => $saleGroups]);
    }

	public function add ()
	{
        $this->authorize('create',$this->policyModel);

		$date = new \DateTime();
		$datenow = $date->format('Y-m-01');
		//trim user data
		$user = Bonus::all()->pluck('erp_user_id')->unique()->values();

		$user = $user->map(function($v,$k) use($datenow){
			$new['erp_user_id'] = $v;
			$new['boundary'] = SaleGroupsUsers::where(['set_date' => $datenow,'erp_user_id' => $v])->first()->getUserBonusBoundary->boundary ?? 0;
			$new['name'] = Cache::get('users')[$v]['name'];
			$saleGroupIds = SaleGroupsUsers::where('erp_user_id',$v)->get()->pluck('sale_groups_id')->unique();
			$new['sale_groups_name'] = implode(',',SaleGroups::whereIn('id',$saleGroupIds)->get()->pluck('name')->toArray());
		 return $new;
		})->sortBy('sale_groups_name')->values()->toArray();

		return view('saleGroup.setting.add',['data' => $this->resources,'user'=> $user
		]);
	}

	public function edit (SaleGroups $saleGroups)
	{
        $this->authorize('create',$this->policyModel);

		$date = new \DateTime();
		$datenow = $date->format('Y-m-01');
		
		//get nowdata
		$row = $saleGroups->toArray();
		$row['groups_bonus']  = $saleGroups->groupsBonus->where('set_date',$datenow)->values()->toArray();
		$row['groups_users']  = $saleGroups->groupsUsers->where('set_date',$datenow)->values();
		$totalBoundary = $row['groups_users']->map(function($v,$k){
			return $v->getUserBonusBoundary;
		})->sum('boundary');
		
		$groupsBonusHistory = $saleGroups->groupsBonus->groupBy('set_date')->map(function($v){
		 return ['bonuslevel' => $v,'rate' => 0,'totalBoundary' => 0];
		})->toArray();
		$saleGroupsRate = $saleGroups->saleGroupsRate;
		
		$saleGroups->groupsUsers->map(function($v,$k) use(&$groupsBonusHistory,$saleGroupsRate){
			$v['boundary'] = $v->getUserBonusBoundary->boundary ?? 0;
			$v['name'] =  $v->user ? ucfirst($v->user->name) : '';
			
			if(isset($groupsBonusHistory[$v->set_date])){
                $groupsBonusHistory[$v->set_date]['totalBoundary'] += $v['boundary'];
                $groupsBonusHistory[$v->set_date]['user'][] =  $v;
                /*抓取rate 對應資料*/
                try {
	                $groupsBonusHistory[$v->set_date]['rate'] = $saleGroupsRate->where('set_date',$v->set_date)->first()['rate'];
                }catch (\Exception $ex) {
                    echo "sale_groups_id {$saleGroupsRate[0]->sale_groups_id} not exist saleGroupsRate set_date {$v->set_date}";
                    die;
                }
            }
		});
		
		$tmpUser = collect($row['groups_users']);
		// get already select
		$userNowSelect = $tmpUser->pluck('erp_user_id');
		$userNowIsConvener = $tmpUser->filter(function($v){
			return $v['is_convener'];
		})->map(function($v){
				return $v['erp_user_id'];
		})->values();
		
		//trim user data
		$user = Bonus::where('id','!=',0)->groupby('erp_user_id')->get();
		$userNowSelectArray =$userNowSelect->toArray();
		$userNowIsConvenerArray = $userNowIsConvener->toArray();
		$user = $user->map(function($v,$k) use($userNowSelectArray,$userNowIsConvenerArray,$datenow){
			$erpUserId= $v->erp_user_id;
			$new['erp_user_id'] = $erpUserId;
			$new['groups_users'] = in_array($erpUserId,$userNowSelectArray) ? 1 : 0;
			$new['groups_is_convener'] = in_array($erpUserId,$userNowIsConvenerArray) ? 1 : 0;
			$new['boundary'] = SaleGroupsUsers::where(['set_date' => $datenow,'erp_user_id' => $erpUserId])->first()->getUserBonusBoundary->boundary ?? 0;
			$userName = User::where('erp_user_id',$erpUserId)->first()->name ?? $erpUserId;
			$new['name'] = $userName;
			$saleGroupIds = SaleGroupsUsers::where('erp_user_id',$erpUserId)->get()->pluck('sale_groups_id')->unique();
			$new['sale_groups_name'] = implode(',',SaleGroups::whereIn('id',$saleGroupIds)->get()->pluck('name')->toArray());
			return $new;
		})->sortBy('sale_groups_name')->values()->toArray();
		
		return view('saleGroup.setting.edit',[
		 'data' => $this->resources,
		 'user'=> $user,
		 'row'=>$row,
		 'totalBoundary' => $totalBoundary,
		 'groupsBonusHistory' => $groupsBonusHistory,
		 'userNowSelect' => $userNowSelect,
		 'userNowIsConvener' => $userNowIsConvener,
		 'nowRate' => $groupsBonusHistory[$datenow]['rate'],
		]);
	}

	public function view ($erpUserId = null)
	{
        $this->authorize('view',$this->policyModel);

		$date = new \DateTime();
		$datenow = $date->format('Y-m-01');
		$erpUserId = $erpUserId ?? Auth::user()->erp_user_id;
		$saleGroupsUsers = SaleGroupsUsers::where(['erp_user_id' => $erpUserId,'is_convener' => 1])
		                    ->orderBy('set_date','desc')->get();

		$saleGroupsIds = $saleGroupsUsers->groupBy('sale_groups_id')->map(function($v,$k) use($datenow){
			return $v->reject(function($v2) use($v,$datenow){
				return $v2['set_date'] < $datenow;
			});
		})->keys();

		$saleGroups = SaleGroups::whereIn('id',$saleGroupsIds)->get();

		//get nowdata
		$row = $saleGroups->toArray();

		foreach ($saleGroups as $key => $saleGroup){
			$row[$key]['groupsBonusHistory'] = $saleGroup->groupsBonus->groupBy('set_date')->toArray();
			$row[$key]['groupsUsersHistory'] = $saleGroup->groupsUsers->groupBy('set_date');
		}
		//trim user data

		$user = Cache::get('users');

		return view('saleGroup.setting.view',[
		 'data' => $this->resources,
		 'user'=> $user,
		 'row'=>$row,
		]);
	}

	public function post (Request $request,$type ='add')
	{
        $this->authorize('create',$this->policyModel);

		$inputDatas = collect($request->all())->forget(['_token','user_table_length']);
		
		//get date
		$date = new \DateTime();
		//$inputDatas['set_date'] = '2019-07-01';
		$setDate = isset($inputDatas['set_date']) ? $inputDatas['set_date'] : $date->format('Y-m-01');

		//set default columns
		if(isset($inputDatas['groupsBonus'])){
			$inputDatas['groupsBonus'] = collect($inputDatas['groupsBonus'])->map(function($v) use($setDate){
				$v['set_date'] = $setDate;
				return $v;
			});
		};

		//trim data
        $tmpIsConvener = [];
        if(!empty($inputDatas['user_now_select_is_convener'])){
            $tmpIsConvener = explode(',',$inputDatas['user_now_select_is_convener']);
        }

        if(!empty($inputDatas['user_now_select'])){
            $inputDatas['groupsUsers'] = collect(explode(',',$inputDatas['user_now_select']))->map(function($v) use($setDate,$tmpIsConvener){
                $arr = [];
                $arr['erp_user_id'] = $v;
                $arr['set_date'] = $setDate;
                $arr['is_convener'] = in_array($v,$tmpIsConvener) ? 1 : 0;
                return $arr;
            })->keyBy('erp_user_id');
        }


		$inputDatas->forget(['user_now_select','user_now_select_is_convener']);
		$inputDatas = $inputDatas->toArray();
		//add
		if($inputDatas['sale_group_id'] == 0){
			$saleGroups = SaleGroups::create($inputDatas);
		}else{
			//edit
			$saleGroups = SaleGroups::findOrFail($inputDatas['sale_group_id']);
			$saleGroups->name = $inputDatas['name'];
			$saleGroups->save();
			$saleGroups->groupsUsers()->where('set_date',$setDate)->delete();
			$saleGroups->groupsBonus()->where('set_date',$setDate)->delete();
		}
		//save children
		foreach (['groupsUsers','groupsBonus'] as $keyfiled){
			if(isset($inputDatas[$keyfiled])){
				$saleGroups->$keyfiled()->createMany($inputDatas[$keyfiled]);
			}
		}
		$saleGroups->saleGroupsRate->where('set_date',today()->format('Y-m-01'))->first()['rate'] = $inputDatas['rate'];
		$saleGroups->push();
		
		$message= [
		 'status' => 0,
		 'status_string' => '',
		 'message' => ''
		];
		$message['status_string'] = 'success';
		$message['message'] = '更新成功';

		return view('handle',['message'=>$message,'data' => $this->resources,'returnUrl' => Route('saleGroup.setting.edit',[$saleGroups->id]) ]);
	}
}

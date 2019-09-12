<?php

namespace App\Http\Controllers\SaleGroup;

use App\SaleGroups;
use App\SaleGroupsBonusLevels;
use App\SaleGroupsUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Input;

class SaleGroupController extends BaseController
{
    //
	public function __construct ()
	{
		parent::__construct();
	}
	
	public function add ()
	{
		$user = session('users');
		sort($user);
		return view('saleGroup.setting.add',['data' => $this->resources,'user'=> $user
		]);
	}
	
	public function edit ($id)
	{

		//$this->resources['cssPath'][] = '/plugins/material/material.min.css';
		//$this->resources['cssPath'][] = 'https://fonts.googleapis.com/icon?family=Material+Icons';
		//$this->resources['jsPath'][] = '/plugins/material/material.min.js';
		//$this->resources['cssPath'][] = '/css/glyphicons.css';
		//$this->resources['cssPath'][] = '/plugins/material/dataTables.material.css';
		//$this->resources['jsPath'][] = '/plugins/material/dataTables.material.js';
		
		
		$date = new \DateTime();
		$datenow = $date->format('Y-m-01');
		
		$saleGroups = SaleGroups::where('id',$id)->with('groupsUsers')->with('groupsBonus')->first();
		
		//get nowdata
		$row = $saleGroups->toArray();
		$row['groups_bonus']  = $saleGroups->groupsBonus->where('set_date',$datenow)->values()->toArray();
		$row['groups_users']  = $saleGroups->groupsUsers->where('set_date',$datenow)->values();
		$totalBoundary = $row['groups_users']->map(function($v,$k){
			return $v->getUserBonusBoundary;
		})->sum('boundary');
		
		$groupsBonusHistory = $saleGroups->groupsBonus->groupBy('set_date')->toArray();
		$groupsUsersHistory = $saleGroups->groupsUsers->groupBy('set_date');
		
		$tmpUser = collect($row['groups_users']);
		// get already select
		$userNowSelect = $tmpUser->pluck('erp_user_id');
		$userNowIsConvener = $tmpUser->filter(function($v){
			return $v['is_convener'];
		})->map(function($v){
				return $v['erp_user_id'];
		})->values();
		
		//trim user data
		$user = session('users');
		$userNowSelectArray =$userNowSelect->toArray();
		$userNowIsConvenerArray = $userNowIsConvener->toArray();
		foreach($user as $key => $item){
			$user[$key]['groups_users'] = in_array($item['id'],$userNowSelectArray) ? 1 : 0;
			$user[$key]['groups_is_convener'] = in_array($item['id'],$userNowIsConvenerArray) ? 1 : 0;
			$user[$key]['boundary'] = SaleGroupsUsers::where(['set_date' => $datenow,'erp_user_id' => $item['id']])->first()->getUserBonusBoundary->boundary ?? 0;
		}
		//$user = collect($user)->filter(function($v,$k){
		//	return $v['boundary'] != 0;
		//})->toArray();
		
		sort($user);
		return view('saleGroup.setting.edit',[
		 'data' => $this->resources,
		 'user'=> $user,
		 'row'=>$row,
		 'totalBoundary' => $totalBoundary,
		 'groupsBonusHistory' => $groupsBonusHistory,
		 'groupsUsersHistory' => $groupsUsersHistory,
		 'userNowSelect' => $userNowSelect,
		 'userNowIsConvener' => $userNowIsConvener
		]);
	}
	public function list ()
	{
		$saleGroups = SaleGroups::all();
		return view('saleGroup.setting.list',['data' => $this->resources,'row' => $saleGroups]);
	}
	
	public function view ($erp_user_id = null)
	{
		$date = new \DateTime();
		$datenow = $date->format('Y-m-01');
		
		$erp_user_id = $erp_user_id ?? session('userData')['id'];
		$saleGroupsUsers = SaleGroupsUsers::where(['erp_user_id' => $erp_user_id,'is_convener' => 1])
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
		$user = session('users');
		return view('saleGroup.setting.view',[
		 'data' => $this->resources,
		 'user'=> $user,
		 'row'=>$row,
		]);
		//return view('saleGroup.setting.view',['data' => $this->resources]);
	}
	
	public function post (Request $request,$type ='add')
	{
		$inputDatas = $request->request->all();
		
		$inputDatas = collect($inputDatas);
		$inputDatas->forget(['_token','user_table_length']);
		
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
		$tmpIsConvener = explode(',',$inputDatas['user_now_select_is_convener']);
		$inputDatas['groupsUsers'] = collect(explode(',',$inputDatas['user_now_select']))->map(function($v) use($setDate,$tmpIsConvener){
			$arr = [];
			$arr['erp_user_id'] = $v;
			$arr['set_date'] = $setDate;
			$arr['is_convener'] = in_array($v,$tmpIsConvener) ? 1 : 0;
			return $arr;
		})->keyBy('erp_user_id');
		
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
		$saleGroups->push();
		
		$message= [
		 'status' => 0,
		 'status_string' => '',
		 'message' => ''
		];
		$message['status_string'] = 'success';
		$message['message'] = '更新成功';
		
		return view('handle',['message'=>$message,'data' => $this->resources,'returnUrl' => Route('saleGroup.setting.edit',['id'=>$saleGroups->id]) ]);
	}
}

<?php

namespace App\Http\Controllers\Financial;
ini_set('max_execution_time', 180);

use App\Bonus;
use App\FinancialList;
use App\Http\Controllers\Auth\Permission;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\FinancialController;
use App\Provide;
use App\SaleGroups;
use App\SaleGroupsReach;
use App\SaleGroupsUsers;
use DateTime;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Route;
use Illuminate\Support\Facades\Input;

class ProvideController extends BaseController
{
    //
	protected $cacheKeyProvide = 'financial.provide';
	protected $cacheKeyFinancial = 'financial.list';
	protected $policyModel;
	
	public function __construct () {
		
		parent::__construct();
		
		$this->policyModel = new Provide();
	}
	
	public function list ()
	{
		
		$this->authorize('viewSetting',$this->policyModel);
		
		/*check cache exists*/
		$cacheData = collect([]);
		
		if (!Cache::store('memcached')->has($this->cacheKeyProvide)) {
			$erpUSerId = Bonus::all()->pluck('erp_user_id')->unique()->values();
			$bonuslist = FinancialList::where('status',1)->where('profit','<>',0)->whereIn('erp_user_id',$erpUSerId)->get();
			$bonuslist = $bonuslist->map(function ($v, $k) {
				$v['receipt_date'] = $v->receipt->created_at->format('Y-m-d');
				$v['sale_group_name'] = $v->saleGroups->saleGroups->name ?? '';
				$v['user_name'] =  ucfirst($v->user->name);
				$v['rate'] = $v->bonus->bonusReach->reach_rate ?? 0;
				$v['profit'] = $this->exchangeMoney($v);
				$v['provide_money'] = round($v['profit'] * $v['rate'] / 100);
				$v['set_date'] = substr($v['set_date'],0,7);
				$v['user_resign_date'] = session('users')[$v->erp_user_id]['user_resign_date'];
				return $v;
			})->values();
			
			$saleGroupsReach = SaleGroupsReach::where('status',0)->get();
			$saleGroupsReach = $saleGroupsReach->map(function($v,$k){
				$v->user_name =  ucfirst($v->saleUser->user->name);
				$v->group_name = $v->saleGroups->name;
				$v->set_date = substr($v->set_date,0,7);
				return $v;
			})->toArray();
			
			Cache::store('memcached')->put($this->cacheKeyProvide, ["bonuslist" =>$bonuslist, 'saleGroupsReach' => $saleGroupsReach],( 8 * 3600 ));
		}
		$cacheData = Cache::store('memcached')->get($this->cacheKeyProvide);
		
		$bonuslist = $cacheData['bonuslist'];
		$saleGroupsReach = $cacheData['saleGroupsReach'];
		
		
		$saleGroupsTableColumns =
		 [
		  ['data' => 'id'],
			['data' => 'set_date'],
			['data'=> 'user_name'],
			['data'=> 'group_name'],
			['data'=> 'groups_profit'],
			['data'=> 'rate'],
			['data'=> 'provide_money','render' => '<div data-money="${data}">${data}</div>']
		 ];
		
		$bonuslistColumns =
		 [
		  ['data' => 'id'],
		  //['data' => 'receipt_date'],
			['data'=> 'set_date'],
			['data'=> 'user_name','render' => '<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="離職日${row.user_resign_date}"><a>${data}</a></span>'],
			['data'=> 'sale_group_name'],
			['data'=> 'campaign_name','render' => sprintf('<a href="http://%s/jsadwaysN2/campaign_view.php?id=${row.campaign_id}" target="_blank">${row.campaign_name}</a>',env('ERP_URL'))],
			['data'=> 'media_channel_name'],
			['data'=> 'sell_type_name'],
			['data'=> 'profit'],
			['data'=> 'rate'],
			['data'=> 'provide_money','render' => '<div data-money="${data}">${data}</div>'],
		 ];
		
		$autoSelectIds = $this->getProvideBalanceSelectedId($bonuslist);
		$total_mondey = $bonuslist->whereIn('id',$autoSelectIds)->sum('provide_money');
		
		return view('financial.provide.list',
		 [
			'data' => $this->resources ,
			'saleGroupsReach' => $saleGroupsReach,
			'saleGroupsTableColumns' => $saleGroupsTableColumns,
			'bonuslistColumns' => $bonuslistColumns,
			'bonuslist' => $bonuslist,
		  'autoSelectIds' => $autoSelectIds,
		  'total_mondey' => $total_mondey ,
		 ]);
		
		//columns : [
    //                {data: "groups_users", render: '<p class="hidden">${data}</p><input id="checkbox${row.erp_user_id}" class="groupsUsers" type="checkbox" value=${row.erp_user_id} ${checkt}>',parmas:'let checkt = data == 1 ? "checked" : "" '},
    //                {data: "groups_is_convener", render: '<p class="hidden">${data}</p><input class="is_convener" type="checkbox" value=${row.erp_user_id} ${checkt}>',parmas:'let checkt = data == 1 ? "checked" : "" '},
    //                {data: "name", render: '<label class="point" for=checkbox${row.erp_user_id}>${data}</label>'},
    //                {data: "boundary", render: '<label class="point" data-boundary_id=${row.erp_user_id} data-boundary=${data} for=checkbox${row.erp_user_id}>${data}</label>'},
    //                {data: "sale_groups_name", render: '<label class="point" for=checkbox${row.erp_user_id}>${data}</label>'}
    //            ],
		
		


	}
	
	public function view ()
	{
		$date = new DateTime(date('Ym01'));
		$erpUserId = Auth::user()->erp_user_id;
		//
//		$provideStart = '2019-09-01';
//		$provideEnd = '2019-09-01';
//		$saleGroupIds =[1,2,3,4];
//		$userIds = [];
//		$request = new Request(['startDate'=>$provideStart,'endDate'=>$provideEnd,'saleGroupIds'=>$saleGroupIds,'userIds'=>$userIds]);
//		dd($this->getAjaxProvideData($request));
		
		list($saleGroups, $userList) = $this->getListData($erpUserId, $date);
		
		//$provideStart = new DateTime();
		//$provideEnd = new DateTime();
		//$userIds = collect($userList)->pluck('erp_user_id')->toArray();
		//$saleGroupsReach = $this->getSaleGroupProvide($provideStart, $provideEnd, $userIds);
		//$provideBonus = $this->getUserBounsProvide($provideStart, $provideEnd, $userIds);
		
		$provideBonusColumns =
		 [
			['data' => 'provide_set_date'],
			['data'=> 'set_date'],
		  ['data'=> 'user_name'],
		  ['data'=> 'sale_group_name'],
		  ['data'=> 'campaign_name','render' => sprintf('<a href="http://%s/jsadwaysN2/campaign_view.php?id=${row.campaign_id}" target="_blank">${row.campaign_name}</a>',env('ERP_URL'))],
			['data'=> 'media_channel_name'],
			['data'=> 'sell_type_name'],
		  ['data'=> 'profit'],
		  ['data'=> 'rate'],
			['data'=> 'provide_money'],
		 ];
		
		$saleGroupsReachColumns =
		 [
			['data' => 'provide_set_date'],
			['data'=> 'set_date'],
			['data'=> 'user_name'],
			['data'=> 'sale_group_name'],
			['data'=> 'groups_profit'],
			['data'=> 'rate'],
			['data'=> 'provide_money'],
		 ];
		
		return view('financial.provide.view',
		 [
          'data' => $this->resources,
		  'provideBonusColumns'=> $provideBonusColumns,
		  'provideBonus'=>[],
		  'saleGroupsReachColumns' => $saleGroupsReachColumns,
		  'saleGroupsReach'=>[],
		  'saleGroups' => $saleGroups,
		  'userList' => $userList]);
	}
	public function getAllSelectId ()
	{
		$row = FinancialList::where(['status' => '0'])->select('id')->pluck('id');
		
		return $row;
	}
	
	public function ajaxCalculatFinancialBonus ()
	{
		
		$selectFincialIds = Input::post('select_financial_ids') ?? [];
		$selectFincialIds = explode(',', $selectFincialIds);
		
		$financialData = FinancialList::join('users', 'financial_lists.erp_user_id', '=', 'users.erp_user_id')
		 ->leftJoin('bonus', function ($join) {
			 $join->on('financial_lists.erp_user_id', '=', 'bonus.erp_user_id')
				->on('financial_lists.set_date', '=', 'bonus.set_date');
		 })
		 ->leftJoin('bonus_reach', function ($join) {
			 $join->on('bonus.id', '=', 'bonus_reach.bonus_id');
		 })
		 ->leftJoin('financial_provides', function ($join) {
			 $join->on('financial_lists.id', '=', 'financial_provides.financial_lists_id');
		 })
		 ->select('financial_provides.created_at as provide_date', 'bonus.id as bonus_id', 'bonus_reach.reach_rate', 'users.name', 'financial_lists.*')
		 ->whereIn('financial_lists.id',$selectFincialIds)
		 ->get();
		
		$financialData = $financialData->map(function($v,$k){
			if(!empty($v['reach_rate']) && $v['profit'] > 0){
				$exchangeProfitMoney = $this->exchangeMoney($v);
				
				$bonusReach = isset($v->bonus) ? $v->bonus->bonusReach : [];
				$reachRate = $bonusReach->reach_rate ?? 0;
				return $exchangeProfitMoney * $reachRate / 100;
			}
		});
		
		
		echo round($financialData->sum());
	}

	public function post(Request $request)
	{
		$this->authorize('create',$this->policyModel);
		
		$selectSaleGroupsReachIds = explode(',',$request->provide_sale_groups_bonus);
		$this->setSaleGroupsReachProvide($selectSaleGroupsReachIds);
		
		$selectFincialIds = $request->provide_bonus;
		$selectFincialIds = $selectFincialIds != null ? explode(',',$selectFincialIds) : [];
		$this->resetFinancialStatus();
		$this->save($selectFincialIds);
		
		$this->cacheRelease();
		
		$message['status_string'] = 'success';
		$message['message'] = '更新成功';
		
		
		return view('handle',['message'=>$message,'data' => $this->resources,'returnUrl' => Route('financial.provide.list') ]);
	}
	/**
	 * @param array $selectFincialIds
	 */
	private function save (array $selectFincialIds): void
	{
		$createdTime = new DateTime();
		if($createdTime->format('d') <= 5){
			$createdTime->modify('-1Month');
		}
		
		$financialList = FinancialList::whereIn('id', $selectFincialIds)->get();
		//add && update
		$financialList->map(function ($v)  use($createdTime){
			//save financialList
			$v->status = 2;
			$v->save();
			$v->refresh();
			
			//calculat exchangeProfit
			$exchangeProfitMoney = $this->exchangeMoney($v);
			
			$financial_lists_id = $v->id;
			$bonusReach = isset($v->bonus) ? $v->bonus->bonusReach : [];
			$bonusId = $bonusReach->bonus_id ?? 0;
			$reachRate = $bonusReach->reach_rate ?? 0;
			$provideMoney = $exchangeProfitMoney * $reachRate / 100;
			
			
			$provide = Provide::where('financial_lists_id', $financial_lists_id)->first();
			
			$provideData = [
			 'bonus_id' => $bonusId,
			 'financial_lists_id' => $financial_lists_id,
			 'provide_money' => $provideMoney > 0 ? $provideMoney  : 0,
			 'created_at' => $createdTime->format('Y-m-01'),
			];
			
			if (isset($provide)) {
				//update
				foreach ($provideData as $key => $item) {
					$provide->$key = $item;
				}
				$provide->save();
			} else {
				//new
				Provide::create($provideData);
			}
			
		});
	}
	/**
	 * @param $v
	 * @return FinancialController
	 */
	private function exchangeMoney ($v)
	{
		$fincialList = new FinancialList();
	
		return $fincialList->exchangeMoney($v)->profit;
	}
	
	private function resetFinancialStatus (): void
	{
		$provideFid = Provide::all()->pluck('financial_lists_id');
		FinancialList::WhereIn('id', $provideFid)->update(['status' => 2]);
	}
	
	public function getAjaxProvideData (Request $request)
	{
		$provideStart = new DateTime($request->startDate);
		$provideEnd = new DateTime($request->endDate);
		$saleGroupIds = $request->saleGroupIds;
		$userIds = $request->userIds;
		if(!empty($userIds)){
			$userIds = User::whereIn('id',$userIds)->get()->pluck('erp_user_id')->toArray();
		}
		if($saleGroupIds && $userIds == null){
			$userIds = SaleGroups::with('groupsUsers')->whereIn('id', $saleGroupIds)->get()->map(function ($v, $k) {
				return $v->groupsUsers->pluck('erp_user_id');
			})->flatten()->unique()->values();
		}
		/*cache start*/
		if( $provideStart != $provideEnd){
			$dateRange = date_range($provideStart->format('Y-m-01'),$provideEnd->format('Y-m-01'));
		}
		$dateRange[] = $provideEnd->format('Y-m-01');
		$cacheData = collect([]);
		$dateNow = new DateTime();
		/*check cache exists*/
		/*cache all user erp Id*/
		$allUserErpIds = Cache::store('memcached')->remember('allUserErpId', (4*360), function () {
			return User::all()->pluck('erp_user_id')->toArray();
		});

		foreach($dateRange as $date) {
			$dateTimeObj = new DateTime($date);
			if (!Cache::store('memcached')->has($this->cacheKeyFinancial  . $date)) {
				$saleGroupRowData = $this->getSaleGroupProvide($dateTimeObj, $dateTimeObj, $allUserErpIds, []);
				$bonusRowData = $this->getUserBounsProvide($dateTimeObj, $dateTimeObj, $allUserErpIds, [])->where('profit','<>',0);

				/*TODO::優化快取暫存時間判斷*/
				$date2 = $dateTimeObj;
				$cacheTime = 1;//hr
				$dateDistance = ($dateNow->getTimestamp() - $date2->getTimestamp()) / (60 * 60 * 24) / 365;
				if ($dateDistance > 0.25) { // over 3 month
					Cache::store('memcached')->forever($this->cacheKeyFinancial  . $date, ['saleGroupRowData' => $saleGroupRowData, 'bonusRowData' => $bonusRowData]);
				} else { // close one month
					Cache::store('memcached')->put($this->cacheKeyFinancial  . $date, ['saleGroupRowData' => $saleGroupRowData, 'bonusRowData' => $bonusRowData],($cacheTime * 3600) );
				};
			}
			$cacheData[] = Cache::store('memcached')->get($this->cacheKeyFinancial  . $date);
		}
		$saleGroupRowData = collect([]);
		$bonusRowData = collect([]);

		$cacheData->map(function($v,$setDate) use(&$saleGroupRowData,&$bonusRowData){
			$saleGroupRowData = $saleGroupRowData->concat($v['saleGroupRowData']);
			$bonusRowData = $bonusRowData->concat($v['bonusRowData']);
		});

		$saleGroupRowData = $saleGroupRowData->whereIn('erp_user_id',$userIds)->values()->toArray();

		$bonusRowData = $bonusRowData->whereIn('erp_user_id',$userIds)->values()->toArray();


		echo json_encode(["provide_groups_list" => $saleGroupRowData,"provide_bonus_list"=>$bonusRowData]);
	}
	/**
	 * @param DateTime $provideStart
	 * @param DateTime $provideEnd
	 * @param $userIds
	 * @return SaleGroupsReach[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
	 */
	private function getSaleGroupProvide (DateTime $provideStart, DateTime $provideEnd, $userIds = null , $saleGroupIds = null)
	{
		
		if($saleGroupIds && $userIds == null){
			$userIds = SaleGroups::with('groupsUsers')->whereIn('id', $saleGroupIds)->get()->map(function ($v, $k) {
				return $v->groupsUsers->pluck('erp_user_id');
			})->flatten();
		}
		
		/* sale Groups Bonus*/
		$saleGroupsReach = SaleGroupsReach::with('saleGroups', 'saleUser')->where('status', 1)->whereBetween('updated_at', [$provideStart->format('Y-m-01'), $provideEnd->format('Y-m-31')])->get();
		
		$saleGroupsReach = $saleGroupsReach->whereIn('saleUser.erp_user_id', $userIds);
		$saleGroupsReach = $saleGroupsReach->map(function ($v, $k) {
			$v['erp_user_id'] = $v->saleUser->erp_user_id;
			$v['provide_set_date'] = $v->updated_at->format('Y-m');
			$v['user_name'] =  ucfirst($v->saleUser->user->name);
			$v['sale_group_name'] = $v->saleGroups->name;
			$v['set_date'] = substr($v['set_date'],0,7);
			return $v;
		})->values();
		return $saleGroupsReach;
	}
	
	/**
	 * @param DateTime $provideStart
	 * @param DateTime $provideEnd
	 * @param $userIds
	 * @return FinancialList[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
	 */
	private function getUserBounsProvide (DateTime $provideStart, DateTime $provideEnd, $userIds = null,$saleGroupIds = null)
	{
		
		if($saleGroupIds && $userIds == null){
			$userIds = SaleGroups::with('groupsUsers')->whereIn('id', $saleGroupIds)->get()->map(function ($v, $k) {
				return $v->groupsUsers->pluck('erp_user_id');
			})->flatten();
		}
		
		// financial bonus list
		$provideBonus = FinancialList::with(['provide', 'user'])->get();
		$provideBonus = $provideBonus->whereBetween('provide.created_at', [$provideStart->format('Y-m-01'), $provideEnd->format('Y-m-31')])
		 ->whereIn('erp_user_id', $userIds)->values();
		
		$provideBonus = $provideBonus->map(function ($v, $k) {

			$v['sale_group_name'] = isset($v->saleGroups) ? $v->saleGroups->saleGroups->name : '';
			$v['user_name'] =  ucfirst($v->user->name);
			$v['provide_set_date'] = $v->provide->created_at->format('Y-m');
			$v['provide_money'] = $v->provide->provide_money;
			$v['rate'] = $v->provide->bonusReach->reach_rate ?? 0;
			$v['set_date'] = substr($v['set_date'],0,7);
			return $v;
		})->values();
		return $provideBonus;
	}
	
	/**
	 * @param bool $isConvener
	 * @param $saleGroupsUsers
	 * @param $erpUserId
	 * @param DateTime $date
	 * @return array
	 */
	public function getListData ($erpUserId, DateTime $date): array
	{
		/*permission check select*/
		$isAdmin = User::where('erp_user_id',$erpUserId)->first()->isAdmin();
		$dateStr = $date->format('Y-m-01');
		/*convener check*/
		$saleGroupsUsers = SaleGroupsUsers::where(['erp_user_id'=> $erpUserId,'set_date'=>$dateStr])->first();
		$isConvener = $saleGroupsUsers->is_convener ?? false;
		
		/* 依照權限不同 取的 user list 資料差異
				admin 全取
				convener 取該團隊
				user 取自己
				*/
		$saleGroupsIds = [];
		$saleGroups = [];
		$userList = [];
		$userIds = [];
		
		if ($isAdmin) {
			$saleGroups = SaleGroups::all();
			$userList = Bonus::with('user')->groupBy('erp_user_id')->orderBy('erp_user_id')->get()->map(function($v,$k){
				$newUser = $v->user;
                $newUser->name =  ucfirst($v->user->name);
			 return $newUser;
			});
		} else {
			if ($isConvener) {
				$saleGroups = [$saleGroupsUsers->saleGroups];
				
				$userGroupIds = $saleGroupsUsers->getSameGroupsUser($erpUserId, $dateStr)->pluck('user')->pluck('erp_user_id')->toArray();
				
				$userList = User::whereIn('erp_user_id', $userGroupIds)->get();
			} else {
				$saleGroups = [];
				
				$userList = User::where('erp_user_id', $erpUserId)->get();
			}
		}
		
		return array($saleGroups, $userList->toArray());
	}
	
	/**
	 * @param array $selectSaleGroupsReachIds
	 */
	private function setSaleGroupsReachProvide (array $selectSaleGroupsReachIds): void
	{
		$saleGroupReach = new SaleGroupsReach();
		$saleGroupReach->whereIn('id', $selectSaleGroupsReachIds)->update(['status' => 1]);
	}
	
	private function getProvideBalanceSelectedId ($dataList) {
		$dataList = $dataList->groupBy('erp_user_id');
		$selectIds = $dataList->map(function($v,$erpUserId){
			$isAlive = session('users')[$erpUserId]['user_resign_date'] == '0000-00-00';
			if($isAlive && $v->sum('provide_money') >= 0){
				return $v->pluck('id');
			}
		})->filter()->flatten();
		
		return $selectIds;
	}
	
	private function cacheRelease (): void
	{
		$date = new DateTime();
		Cache::store('memcached')->forget($this->cacheKeyFinancial . $date->format('Y-m-01'));
		Cache::store('memcached')->forget($this->cacheKeyProvide);
		$date->modify('-1Month');
		Cache::store('memcached')->forget($this->cacheKeyFinancial . $date->format('Y-m-01'));
		Cache::store('memcached')->forget($this->cacheKeyProvide);
	}
}


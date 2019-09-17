<?php

namespace App\Http\Controllers\Financial;
ini_set('max_execution_time', 180);

use App\Bonus;
use App\BonusReach;
use App\FinancialList;
use App\Http\Controllers\Auth\Permission;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Bonus\ReviewController;
use App\Http\Controllers\FinancialController;
use App\Provide;
use App\SaleGroups;
use App\SaleGroupsBonusLevels;
use App\SaleGroupsReach;
use App\SaleGroupsUsers;
use DateTime;
use function foo\func;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;
use Illuminate\Support\Facades\Auth;
use Route;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ProvideController extends BaseController
{
    //
	public function list ()
	{
	
//		$userData = [
		////		 'uId' => $id,
		////		 'name' => session('users')[$id]['name'],
		////		 'title' => session('users')[$id]['department_name'],
		////		];
		// material design
		$this->resources['cssPath'][] = '/plugins/material/material.min.css';
		$this->resources['cssPath'][] = 'https://fonts.googleapis.com/icon?family=Material+Icons';
		$this->resources['jsPath'][] = '/plugins/material/material.min.js';
		$this->resources['cssPath'][] = '/css/glyphicons.css';
		
		$page = Input::get('page') ?? 1;
		$sort = Input::get('sort')?? 'DESC';
		$sort_by = Input::get('sort_by')?? 'erp_user_id';
		$showItem = Input::get('showItem') ?? 500;
		$searchStr = Input::get('searchStr') ?? '';
		
		$date = new \DateTime();
		
		list($newRow,$paginate,$allId,$selectIds,$totalAlredaySelectMoney) = $this->getDataBackend('add',$date->format('Y-m-01'));
		
		//dd(UrlWindow::make($paginate->onEachSide(1)));
//		dd($paginate->count(),$paginate->getUrlRange(5, 10),
//$paginate->currentPage(),
//$paginate->firstItem(),
//$paginate->getOptions(),
//$paginate->hasMorePages(),
//$paginate->lastItem(),
//$paginate->lastPage(),
//$paginate->nextPageUrl(),
//$paginate->onFirstPage(),
//$paginate->perPage(),
//$paginate->previousPageUrl(),
//$paginate->total());
		
		//'allId' => count($allId) ? $allId : [],
		//  'selectIds'=> count($selectIds) ? $selectIds : [],
		//  'paginate'=> count($paginate) ? $paginate : [],
		$columns =
		 [
		  ['data' => 'id'],
			['data' => 'set_date'],
			['data'=> 'user_name'],
			['data'=> 'group_name'],
			['data'=> 'status','render'=>'團績獎金'],
			['data'=> 'groups_profit'],
			['data'=> 'rate'],
			['data'=> 'provide_money']
		 ];

		//:table_title='["月份","業務","團隊名稱","類型","團隊毛利","獎金比例","獎金"]'
		$saleGroupsReach = SaleGroupsReach::where('status',0)->get();
		$saleGroupsReach = $saleGroupsReach->map(function($v,$k){
		 $v->user_name = $v->saleUser->user->name;
			$v->group_name = $v->saleGroups->name;
			return $v;
		})->toArray();

		return view('financial.provide.list',
		 [
		  'data' => $this->resources ,
		  'row' => $newRow ? $newRow : [],
		  'saleGroupsReach' => $saleGroupsReach,
		  'saleGroupsTableColumns' => $columns,
		  'allId' => count($allId) ? $allId : [],
		  'selectIds'=> $selectIds,
		  'paginate'=> $paginate,
		  'sort'=>$sort,
		  'sort_by'=>$sort_by,
		  'search_str'=>$searchStr,
		  'paginateElement'=>Arr::flatten(UrlWindow::make($paginate->onEachSide(1))),
		  'totalAlredaySelectMoney' => $totalAlredaySelectMoney]);
	}
	
	public function view ($id= null)
	{

		$date = new DateTime(date('Ym01'));
		$nowUser = Auth::user();
		$uid= $nowUser->erp_user_id;
		
		$provideStart = new DateTime();
		$provideEnd = new DateTime();
		
		list($saleGroups, $userList, $userIds) = $this->getListData($uid, $date);
		
		$saleGroupsReach = $this->getSaleGroupProvide($provideStart, $provideEnd, $userIds);
		$provideBonus = $this->getUserBounsProvide($provideStart, $provideEnd, $userIds);
		
		$provideBonusColumns =
		 [
			['data' => 'provide_set_date'],
			['data'=> 'set_date'],
			['data'=> 'campaign_name'],
			['data'=> 'user_name'],
			['data'=> 'sale_group_name'],
			['data'=> 'media_channel_name'],
			['data'=> 'sell_type_name'],
			['data'=> 'provide_money'],
		 ];
		
		$saleGroupsReachColumns =
		 [
			['data' => 'provide_set_date'],
			['data'=> 'set_date'],
			['data'=> 'user_name'],
			['data'=> 'sale_group_name'],
			['data'=> 'status','render'=>'團績獎金'],
			['data'=> 'groups_profit'],
			['data'=> 'rate'],
			['data'=> 'provide_money'],
		 ];
		
		return view('financial.provide.view',
		 [
			'data' => $this->resources,
		  'provideBonusColumns'=> $provideBonusColumns,
		  'provideBonus'=>$provideBonus,
		  'saleGroupsReachColumns' => $saleGroupsReachColumns,
		  'saleGroupsReach'=>$saleGroupsReach,
		  'saleGroups' => $saleGroups,
		  'userList' => $userList]);
	}
	public function getAllSelectId ()
	{
		$row = FinancialList::where(['status' => '0'])->select('id')->pluck('id');
		
		return $row;
	}
	
	public function getDataBackend ($type = 'add' ,$startDate , $endDate = null)
	{
		$startDate = $startDate ?? 'all';
		$output = 'return';
		//$startDate = '2019-08-01';
		//$type = 'view';
		$parmas = [
			'pgae' => Input::get('page') ?? 1,
			'sort' => Input::get('sort')?? 'DESC',
			'sort_by' => Input::get('sort_by')?? 'erp_user_id',
			'showItem' => Input::get('showItem') ?? 500,
			'searchStr' => Input::get('searchStr') ?? '',
			'startDate' => $startDate,
			'endDate' => $endDate ?? null
		];
		
		return $this->getData($output, $type, $parmas);
	}
	public function getAjaxData ()
	{
		$type = Input::get('type') ?? 'add';
		$output = 'echo';
		
		$parmas = [
		 'pgae' => Input::get('page') ?? 1,
		 'sort' => Input::get('sort')?? 'DESC',
		 'sort_by' => Input::get('sort_by')?? 'erp_user_id',
		 'showItem' => Input::get('showItem') ?? 500,
		 'searchStr' => Input::get('searchStr') ?? '',
		 'startDate' => Input::get('startDate') ?? 'all',
		 'endDate' => Input::get('endDate') ?? null,
		];
		return $this->getData($output, $type, $parmas);
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
	public function getData ($output = 'echo' ,$type = 'add' ,$parmas)
	{
		extract($parmas);
		
		//search
		$numberColumm = ['`set_date`','`income`','`cost`','`profit`'];
		$strColumm = ['`campaign_name`','`media_channel_name`','`sell_type_name`','`organization`','`currency`','`users`.`name`'];
		
		if(isDateSimple($searchStr)||is_numeric($searchStr)){
			$newColumm = array_merge($numberColumm,$strColumm);
		}else{
			$newColumm = $strColumm;
		}
		$selectIds = [];
		$allId = $this->returnList($newColumm, $searchStr, $sort_by, $sort)->where(function($q){
			$q->where('status',1)->orwhere(['status' => 2]);
		})->pluck('id');
		
		if($type == 'add'){
			if($startDate == 'all'){
				$paginate = $this->returnList($newColumm, $searchStr, $sort_by, $sort)->where(function($q){
					$q->where('status',1)->orwhere(['status' => 2]);
				});
				$selectIds = $this->returnList($newColumm, $searchStr, $sort_by, $sort)->where(['status' => 2]);
			
			}else if(empty($endDate) && !empty($startDate)){
				
				$date = new \DateTime($startDate);
				$paginate = $this->returnList($newColumm, $searchStr, $sort_by, $sort)->where(function($q){
					$q->where('status',1)->orwhere(['status' => 2]);
				})->where(function($q) use($date){
					$q->where('financial_provides.created_at','like', $date->format('Y-m').'%')->OrWhereNull('financial_provides.created_at');
				});
				$selectIds = $this->returnList($newColumm, $searchStr, $sort_by, $sort)->where(['status' => 2])->where('financial_provides.created_at', 'like', $date->format('Y-m').'%')->pluck('id');
			}else if(!empty($endDate) && !empty($startDate)){
				
				$paginate = $this->returnList($newColumm, $searchStr, $sort_by, $sort)->whereBetween('financial_provides.created_at', [$startDate,$endDate])->where(['status' => 2]);
				$selectIds = $this->returnList($newColumm, $searchStr, $sort_by, $sort)->where(['status' => 2])->whereBetween('financial_provides.created_at', [$startDate,$endDate])->pluck('id');
			}
		};
		
		if($type == 'view'){
			
			if($startDate == 'all'){
				$paginate = $this->returnList($newColumm, $searchStr, $sort_by, $sort)->where('status',2);
			}else if(empty($endDate) && !empty($startDate)){
				$date = new \DateTime($startDate);
				$paginate = $this->returnList($newColumm, $searchStr, $sort_by, $sort)->where('status',2)->where('financial_provides.created_at', 'like', $date->format('Y-m').'%');
				
			}else if(!empty($endDate) && !empty($startDate)){
				$paginate = $this->returnList($newColumm, $searchStr, $sort_by, $sort)->where('status',2)->whereBetween('financial_provides.created_at', [$startDate,$endDate] );
			}
		};
		$totalAlredaySelectMoney = 0;
		$paginate = $paginate->with('provide')->paginate($showItem);
		
		$paginate->map(function($v,$k) use(&$newRow,&$totalAlredaySelectMoney){
			$v['profit'] = $this->exchangeMoney($v);
			
			$totalAlredaySelectMoney += $v->provide->provide_money ?? 0;
			
			$newRow[$v['erp_user_id']][$v['campaign_id']][] = $v;
		});
		
		if($output == 'echo'){
			
			echo json_encode(['row' => $newRow, 'paginate' => $paginate]);
		}else if($output == 'return'){
			return [$newRow,$paginate,$allId,$selectIds,$totalAlredaySelectMoney];
		}
		
	}
	
	public function post()
	{
		
		$selectSaleGroupsReachIds = explode(',',Input::post('sale_sale_groups_reach_ids'));
		$saleGroupReach = new SaleGroupsReach();
		$saleGroupReach->whereIn('id',$selectSaleGroupsReachIds)->update(['status'=>1]);
		
		$selectFincialIds = Input::post('select_financial_ids');
		$selectFincialIds = $selectFincialIds != null ? explode(',',$selectFincialIds) : [];
		
		$originalSelectFinancialIds = Input::post('original_select_financial_ids');
		$originalSelectFinancialIds = $originalSelectFinancialIds != null ? explode(',',$originalSelectFinancialIds) : [];
		
		$this->resetFinancialStatus();
		
		$deleteIds = array_diff($originalSelectFinancialIds,$selectFincialIds);
		
		$this->delete($deleteIds);
		
		$this->save($selectFincialIds);
		
		$message['status_string'] = 'success';
		$message['message'] = '更新成功';
		
		return view('handle',['message'=>$message,'data' => $this->resources,'returnUrl' => Route('financial.provide.list') ]);
	}
	
	/**
	 * @param array $newColumm
	 * @param $searchStr
	 * @param string $sort_by
	 * @param string $sort
	 * @return mixed
	 */
	private function returnList (array $newColumm, $searchStr, string $sort_by, string $sort)
	{
		
		return FinancialList::join('users', 'financial_lists.erp_user_id', '=', 'users.erp_user_id')
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
		 
		 ->where(function ($query) use ($newColumm, $searchStr) {
			 $query->whereRaw(join(' like "%' . $searchStr . '%" OR ', $newColumm) . ' like "%' . $searchStr . '%"');
		 })
		 ->orderBy($sort_by, $sort)->orderBy('campaign_id', 'DESC');
		//->whereOr('financial_provides.created_at', 'like', '2019-08%')
	
	}
	

	/**
	 * @param array $selectFincialIds
	 */
	private function save (array $selectFincialIds): void
	{
	
		$financialList = FinancialList::whereIn('id', $selectFincialIds)->get();
		//add && update
		$financialList->map(function ($v) {
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
			 'provide_money' => $provideMoney > 0 ? $provideMoney  : 0
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
	 * @param $deleteIds
	 */
	private function delete ($deleteIds): void
	{
		FinancialList::whereIn('id', $deleteIds)->update(['status' => 1]);
		Provide::whereIn('financial_lists_id', $deleteIds)->delete();
	}
	
	/**
	 * @param $v
	 * @return FinancialController
	 */
	private function exchangeMoney ($v)
	{
		$fincialController = new FinancialController();
	
		return $fincialController->exchangeMoney($v)->profit;
	}
	
	
	public function exportExcel ()
	{
	
	}
	
	private function resetFinancialStatus (): void
	{
		$provideFid = Provide::all()->pluck('financial_lists_id');
		FinancialList::WhereIn('id', $provideFid)->update(['status' => 2]);
	}
	
	/**
	 * @param DateTime $provideStart
	 * @param DateTime $provideEnd
	 * @param $userIds
	 * @return SaleGroupsReach[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
	 */
	private function getSaleGroupProvide (DateTime $provideStart, DateTime $provideEnd, $userIds)
	{
		/* sale Groups Bonus*/
		$saleGroupsReach = SaleGroupsReach::with('saleGroups', 'saleUser')->where('status', 1)->whereBetween('updated_at', [$provideStart->format('Y-m-01'), $provideEnd->format('Y-m-31')])->get();
		$saleGroupsReach = $saleGroupsReach->whereIn('saleUser.erp_user_id', $userIds);
		$saleGroupsReach = $saleGroupsReach->map(function ($v, $k) {
			$v['provide_set_date'] = $v->updated_at->format('Y-m-d');
			$v['user_name'] = $v->saleUser->user->name;
			$v['sale_group_name'] = $v->saleGroups->name;
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
	private function getUserBounsProvide (DateTime $provideStart, DateTime $provideEnd, $userIds)
	{
		// financial bonus list
		$provideBonus = FinancialList::with(['provide', 'user'])->get();
		$provideBonus = $provideBonus->whereBetween('provide.created_at', [$provideStart->format('Y-m-01'), $provideEnd->format('Y-m-31')])
		 ->whereIn('erp_user_id', $userIds)->values();
		
		$provideBonus = $provideBonus->map(function ($v, $k) {
			$v['sale_group_name'] = $v->saleGroups->saleGroups->name;
			$v['user_name'] = $v->user->name;
			$v['provide_set_date'] = $v->provide->created_at->format('Y-m-d');
			$v['provide_money'] = $v->provide->provide_money;
			return $v;
		})->values();
		return $provideBonus;
	}
	
	/**
	 * @param bool $isAdmin
	 * @param bool $isConvener
	 * @param $saleGroupsUsers
	 * @param $uid
	 * @param DateTime $date
	 * @return array
	 */
	private function getListData ($uid, DateTime $date): array
	{
		/*permission check select*/
		$permission = new Permission();
		/*admin check*/
		$isAdmin = $permission->isAdmin($uid);
		/*convener check*/
		$saleGroupsUsers = SaleGroupsUsers::where(['erp_user_id'=> $uid,'set_date'=>$date])->first();
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
			$userList = session('users');
			$saleGroupsIds = $saleGroups->pluck('id');
			$userIds = SaleGroups::with('groupsUsers')->whereIn('id', $saleGroupsIds)->get()->map(function ($v, $k) {
				return $v->groupsUsers->pluck('erp_user_id');
			})->flatten()->unique();
		} else {
			if ($isConvener) {
				$saleGroups = [$saleGroupsUsers->saleGroups];
				$userGroupIds = $saleGroupsUsers->getSameGroupsUser($uid, $date)->pluck('user')->pluck('erp_user_id');
				$userList = collect(session('users'))->whereIn('id', $userGroupIds);
			} else {
				$saleGroups = [];
				$userList = collect(session('users'))->whereIn('id', $uid);
			}
			$userIds = $userList->pluck('id');
			$userList = $userList->toArray();
		}
		
		sort($userList);
		return array($saleGroups, $userList, $userIds);
	}
}


<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class SaleGroups extends Model
{
    //
	protected $fillable = ['name'];

	public function groupsUsers ()
	{

			return $this->hasMany(SaleGroupsUsers::CLASS)->orderBy('set_date', 'desc')->orderBy('is_convener','desc');

	}

	public function groupsUsersLastMonth ()
	{
		$lastMonth = date('Y-m-01',strtotime("-1 month"));

		return $this->hasMany(SaleGroupsUsers::CLASS)->where('set_date',$lastMonth)->orderBy('is_convener','desc');
	}

	public function groupsBonus ()
	{
		return $this->hasMany(SaleGroupsBonusLevels::CLASS)->orderBy('set_date', 'desc');
	}

	public function groupsBonusLastMonth ()
	{
		$lastMonth = date('Y-m-01',strtotime("-1 month"));

		return $this->hasMany(SaleGroupsBonusLevels::CLASS)->where('set_date',$lastMonth)->orderBy('set_date', 'desc');
	}
	
	public function saleGroupsRate () {
		return $this->hasMany(SaleGroupsRate::Class);
	}
	/**
	 * @param $saleGroupIds
	 * @return array
	 */
	public function getGroupBoundary ($saleGroupIds,String $dateTime)
	{
		$tmpGroups = SaleGroups::whereIn('id', $saleGroupIds)->get()->map(function ($v, $k) use ($dateTime) {
			$dateStart = new DateTime($dateTime);
			$dateStart = $dateStart->format('Y-m-01');
			$sameGroupErpUserIds = SaleGroupsUsers::where(['sale_groups_id' => $v->id, 'set_date' => $dateStart])->get();
			$groupBoundary = $sameGroupErpUserIds->map(function ($v, $k) {
				$v['boundary'] = $v->getUserBonusBoundary->boundary ?? 0;
				return $v;
			})->sum('boundary');
			$v['boundary'] = $groupBoundary;
			$v['set_date'] = $dateStart;
			return $v;
		});
		return $tmpGroups->toArray();
	}
}

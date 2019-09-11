<?php

namespace App;

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
		$lastMonth = '2019-08-01';
		return $this->hasMany(SaleGroupsUsers::CLASS)->where('set_date',$lastMonth)->orderBy('is_convener','desc');
	}
	
	public function groupsBonus ()
	{
		
		return $this->hasMany(SaleGroupsBonusLevels::CLASS)->orderBy('set_date', 'desc');
		
	}
	
	public function groupsBonusLastMonth ()
	{
		$lastMonth = date('Y-m-01',strtotime("-1 month"));
		$lastMonth = '2019-08-01';
		return $this->hasMany(SaleGroupsBonusLevels::CLASS)->where('set_date',$lastMonth)->orderBy('set_date', 'desc');
	}
	
}

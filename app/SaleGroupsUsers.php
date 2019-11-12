<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SaleGroups;

class SaleGroupsUsers extends Model
{
    //
	protected $attributes = [
	 'is_convener' => 0,
	];
	protected $fillable = ['erp_user_id','is_convener','set_date'];
	
	public function getUserSaleGroupsId ($erpUserId , $setDate)
	{
		return $this->where(['erp_user_id'=>$erpUserId,'set_date'=>$setDate])->first()->sale_groups_id;
	}
	
	public function getSameGroupsUser ($erpUserId,$setDate)
	{
		$saleGroupsId = $this->getUserSaleGroupsId($erpUserId,$setDate);
		return $this->where(['sale_groups_id'=>$saleGroupsId,'set_date'=>$setDate])->with('user')->get();
	}
	
	public function getUserBonusBoundary ()
	{
		return $this->hasOne(Bonus::CLASS , 'erp_user_id','erp_user_id')->where('set_date',$this->set_date);
	}
	
	public function saleGroups ()
	{
		return $this->belongsTo(SaleGroups::CLASS);
	}
	
	public function user()
	{
		return $this->hasOne(User::CLASS,'erp_user_id','erp_user_id');
	}
}

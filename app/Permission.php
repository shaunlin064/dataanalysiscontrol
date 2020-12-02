<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Role;

class Permission extends Model
{
    //
	protected $fillable = ['name','label','permissions_class_id'];
	
	public function roles ()
	{
		return $this->belongsToMany(Role::class);
	}
	
    public function permissionClass ()
    {
        return $this->belongsTo(PermissionsClass::class,'permissions_class_id','id');
    }
	
	static function canReviewFinancialAllUserData ( $type ) {
		
		$thisLoginUser = auth()->user();
		
		if ( $thisLoginUser->isAdmin()){
			return true;
		}
		
		if( $type == 'bonus_view' && $thisLoginUser->hasPermission('bonus.review.all_user') ){
			return true;
		}
		
		if( $type == 'provide_view' && $thisLoginUser->hasPermission('provide.view.all_user')){
			return true;
		}
		
		return 	False;
	}
	
	static function canReviewFinancialGroupData($type){
		
		$thisLoginUser = auth()->user();
		
		if ( $thisLoginUser->isAdmin()){
			return true;
		}
		
		if( $type == 'bonus_view' && $thisLoginUser->hasPermission('bonus.review.group_data') ){
			return true;
		}
		
		if( $type == 'provide_view' && $thisLoginUser->hasPermission('provide.review.group_data')){
			return true;
		}
		
		return False;
	}
}

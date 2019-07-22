<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Permission;

class Role extends Model
{
    //
	public function givePermissionTo(Permission $permission){
		
		return $this->permission()->save($permission);//將這個權限保存
		
	}
	
	public function permission(){
		
		return $this->belongsToMany(Permission::class);
	}
	

	
	

}

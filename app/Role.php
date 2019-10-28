<?php

namespace App;

use App\Permission;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //
	protected $fillable = ['name','label'];
	
	public function permissions(){
		
		return $this->belongsToMany(Permission::class);
	}
	
	public function givePermissionTo($permission){
		
		if (is_string($permission)) {
            return $this->permissions()->save(
                Permission::whereName($permission)->firstOrFail()
            );
		}
		
		//如果是Collection
		return $this->permissions()->save($permission);//將這個權限保存
		
	}

}

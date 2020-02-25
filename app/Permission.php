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
	
}

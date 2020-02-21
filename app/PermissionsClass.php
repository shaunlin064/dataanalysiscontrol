<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PermissionsClass extends Model
{
    //
    protected $table = 'permissions_class';
    
    public function permissions()
    {
        return $this->hasMany(Permission::Class, 'permissions_class_id', 'id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    //
	protected $guarded = ['subMenu'];
	
	public function subMenu()
	{
		return $this->hasMany(MenuSub::CLASS);
	}
}

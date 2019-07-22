<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuSub extends Model
{
    //
	protected $guarded = [];
	public function menu()
	{
		return $this->belongsTo(Menu::CLASS);
	}
	
	public function level2()
	{
		
		return $this->hasMany(MenuSubLevel2::CLASS);
	}
}

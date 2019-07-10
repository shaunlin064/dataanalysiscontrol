<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Bonus;
class BonusLevels extends Model
{
    //
	protected $fillable = ['bonus_id','achieving_rate','bonus_rate','bonus_direct'];
	
	public function bonus()
	{
		return $this->hasOne(Bonus::CLASS);
	}
}

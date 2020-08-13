<?php

namespace App;

use App\Bonus;
use Illuminate\Database\Eloquent\Model;

class BonusReach extends Model
{
    //
	protected $table = 'bonus_reach';
	protected $fillable = ['bonus_id','reach_rate'];

	public function bonus()
	{
		return $this->belongsTo(Bonus::CLASS);
	}
}

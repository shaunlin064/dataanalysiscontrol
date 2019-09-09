<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provide extends Model
{
    //
	protected $table = 'financial_provides';
	protected $fillable = ['financial_lists_id','bonus_id','provide_money'];
	
	public function financialList ()
	{
		return $this->hasOne(FinancialList::CLASS,'id','financial_lists_id');
	}
	
	public function bonusReach ()
	{
		return $this->hasOne(BonusReach::CLASS);
	}
	
}

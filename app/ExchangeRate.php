<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    //
	protected $guarded = ['_token'];
    
    static function checkDataExsist ($setDate,$currency)
    {
        return self::where(['set_date'=> $setDate,'currency'=>$currency])->exists();
	}
}

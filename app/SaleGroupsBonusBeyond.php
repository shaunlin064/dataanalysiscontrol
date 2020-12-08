<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleGroupsBonusBeyond extends Model
{
    use HasFactory;
    
    protected $fillable = ['status','sale_groups_id','set_date','provide_money','updated_at'];
    protected $attributes = [
    	'status' => 0,
	    'provide_money' => 0
    ];
	
	public function saleGroup () {
		return $this->belongsTo(SaleGroups::class,'sale_groups_id');
    }
}

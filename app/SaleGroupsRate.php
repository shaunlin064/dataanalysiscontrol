<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleGroupsRate extends Model
{
    use HasFactory;
    
    protected $fillable = ['set_date','rate'];
	
	public function saleGroup () {
		return $this->belongsTo(SaleGroups::Class,'sale_groups_id');
    }
}

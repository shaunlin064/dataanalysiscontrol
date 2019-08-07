<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinancialList extends Model
{
    //

	protected $guarded = ['med_group','dep_name','dep_id','username','priority'];
	protected $attributes = [
	 'status' => 0,
		'profit_percentage' => 0,
	];
	
	protected $keyReplace = [
	  'memberid' => 'erp_id',
		'currency_id' => 'currency',
		'year_month' => 'set_date',
		'cam_id' => 'campaign_id',
		'o_id' => 'cp_detail_id'
	];
	
	
	
	public function __construct ()
	{
		parent::__construct();
	}
	
	public function dataFormat ()
	{
		$this->keyChange()->valueChange();
		return $this;
	}
	
	public function keyChange ()
	{
		$attributes = $this->getAttributes();
		
		foreach ($attributes as $index => $attribute) {
			
			if(in_array($index,array_keys($this->keyReplace))){
				
				$key = $this->keyReplace[$index];
				$attributes[$key] = $attribute;
				unset($attributes[$index]);
				
			}
		};
		$this->setRawAttributes($attributes);
		return $this;
	}
	
	public function revertKeyChange (){
		
		$attributes = $this->getAttributes();
		
		foreach ($attributes as $index => $attribute) {
			
			if(in_array($index,$this->keyReplace)){
				
				$key = array_flip($this->keyReplace)[$index];
				$attributes[$key] = $attribute;
				unset($attributes[$index]);
				
			}
		};
		$this->setRawAttributes($attributes);
		
		return $this;
	}
	
	public function valueChange ()
	{
		$this->set_date = date('Y-m-d',strtotime($this->set_date.'01'));
		if($this->profit_percentage == null){
			$this->profit_percentage = 0;
		}
		return $this;
	}
	
	public function revertValueChange ()
	{
		$this->year_month = date('Ym',strtotime($this->year_month));
		if($this->profit_percentage == null){
			$this->profit_percentage = 0;
		}
		return $this;
	}
}

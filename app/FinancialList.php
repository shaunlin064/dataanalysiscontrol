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
	  'memberid' => 'erp_user_id',
		'currency_id' => 'currency',
		'year_month' => 'set_date',
		'cam_id' => 'campaign_id',
		'o_id' => 'cp_detail_id'
	];
	
	public function __construct ()
	{
		parent::__construct();
	}
	
	public function bonus()
	{
		return $this->hasOne(Bonus::CLASS, 'erp_user_id','erp_user_id')->where('set_date',$this->set_date);
	}
	
	public function provide()
	{
		return $this->hasOne(Provide::CLASS, 'financial_lists_id','id');
	}
	
	public function receipt()
	{
		return $this->hasOne(FinancialReceipt::CLASS, 'financial_lists_id','id');
	}
	
	
	public function user(){
		return $this->hasOne(User::CLASS,'erp_user_id','erp_user_id');
	}
	
	public function dataFormat ()
	{
		$this->keyChange()->valueChange();
		return $this;
	}
	
	public function rawKeyChange ($data)
	{
		foreach ($data as $index => $attribute) {
			
			if(in_array($index,array_keys($this->keyReplace))){
				
				$key = $this->keyReplace[$index];
				$data[$key] = $attribute;
				unset($data[$index]);
			}
		}
		return $data;
	}
	
	public function keyChange ()
	{
		$attributes = $this->getAttributes();
		
		$this->setRawAttributes($this->rawKeyChange($attributes));
		
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
	
	public function updateSet ($id,$data)
	{
		$data = $this->rawKeyChange($data);

		if($data['profit_percentage'] == null){
			$data['profit_percentage'] = 0;
		}
		//guarded
		foreach ($this->guarded as $unsetField){
			unset($data[$unsetField]);
		}
		// ex guarded
		unset($data['set_date']);
		unset($data['cp_detail_id']);
		unset($data['campaign_id']);
		
		if($this->checkDiff($id, $data)){
			return $this->where('id', $id)->update($data);
		}
	}
	
	public function exchangeMoney ($items)
	{
		$set_date = $items->set_date ??  date('Y-m-01',strtotime($items['year_month'].'01'));
		$currency = $items->currency ?? $items['currency_id'];
		$organization = $items->organization ?? $items['organization'];
		$exchangeRate = ExchangeRate::where(['set_date'=> $set_date,'currency'=>$currency])->first();
		
		//台灣傑思後台 只能填 台幣
		if( $organization == 'js' ){
			return $items;
		};
		switch($currency){
			case 'USD':
				if(empty($exchangeRate)){
					$exchangeRate = 31;
					//$exchangeRate = 1;
				}else{
					$exchangeRate = $exchangeRate->rate;
				}
				break;
			case 'JPY':
				if(empty($exchangeRate)){
					$exchangeRate = 0.2875;
					//$exchangeRate = 1;
				}else{
					$exchangeRate = $exchangeRate->rate;
				}
				break;
			default:
				$exchangeRate = 1;
				break;
		}
		if( in_array($currency,['USD','JPY']) ){
			$tmpData = [
			 'income' => round(($items->income ?? $items['income']) * $exchangeRate),
			 'cost' => round(($items->cost ?? $items['cost']) * $exchangeRate),
			];
			$tmpData['profit'] = $tmpData['income'] - $tmpData['cost'];
			
			//TODO 毛利小於零 是否需要扣1%
			if($items->organization ?? $items['organization'] == 'hk'){
				$tmpData['profit']= round($tmpData['profit'] * 0.99);
			}
			switch(gettype($items)){
				case 'object':
					foreach ($tmpData as $key => $item){
						$items->$key = $item;
					}
					break;
				default:
					foreach ($tmpData as $key => $item){
						$items[$key]= $item;
					}
			}
		}
		return $items;
		
	}
	/**
	 * @param $id
	 * @param $data
	 * @return mixed
	 */
	private function checkDiff ($id, $data)
	{
		$orginal = collect($this->where('id', $id)->first()->toArray());
		$orginal->forget('set_date');
		$orginal->forget('cp_detail_id');
		$orginal->forget('campaign_id');
		$orginal->forget('id');
		$orginal->forget('status');
		$orginal->forget('created_at');
		$orginal->forget('updated_at');
		return count($orginal->diffAssoc($data)) > 0 ? true : false;
	}
	
}

<?php

namespace App;

use App\Http\Controllers\FinancialController;
use Illuminate\Database\Eloquent\Model;

class FinancialReceipt extends Model
{
    //
	protected $fillable = ['financial_lists_id','created_at','updated_at'];
	
	public function updateFinancialMoneyReceipt ($type='select')
	{
		$financial = new FinancialController();
		/*cp_detail_id and balance date */
		$erpReturnData = collect($financial->getBalancePayMentData($type));
		$cpDetailIds = $erpReturnData->pluck('cp_detail_id');
		
		$v = FinancialList::whereIn('cp_detail_id',$cpDetailIds)->get();
		FinancialList::whereIn('cp_detail_id',$cpDetailIds)->update(['status' => 1]);
		
		$v->map(function ($v) use($erpReturnData){
			if(!$this->where('financial_lists_id',$v->id)->exists()){
				$this->create(['financial_lists_id' => $v->id,'created_at' => $erpReturnData->where('cp_detail_id',$v->cp_detail_id)->first()['balance_date']]);
			}
		});
	}
}

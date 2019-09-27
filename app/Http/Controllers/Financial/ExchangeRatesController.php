<?php

namespace App\Http\Controllers\Financial;

use App\ExchangeRate;
use App\FinancialReceipt;
use App\Http\Controllers\BaseController;
use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ExchangeRatesController extends BaseController
{
    //
	const CURRENCY_TYPE = [
	 'USD','JPY'
	];
	
	public function setting ()
	{
		$erpUserId = Auth::user()->erp_user_id;
		if(!in_array($erpUserId,session('role')['admin']['ids']) && !in_array($erpUserId,session('role')['financial']['ids'])){
			abort(403);
		};
		
		$financialReceiptObj = new FinancialReceipt();
		$financialReceiptObj->updateFinancialMoneyReceipt();
		
		$row = ExchangeRate::orderBy('set_date','DESC')->get();
		return view('financial.exchangeRate.setting',['data' => $this->resources,'currencys'=> self::CURRENCY_TYPE,'row'=>$row]);
	}
	
	public function add (Request $request)
	{
		$date = new DateTime($request->set_date.'/01');
		$date = $date->format('Y-m-01');
		$erpUserId = Auth::user()->erp_user_id;
		$request->request->set('set_date',$date);
		$request->request->set('created_by_erp_user_id',$erpUserId);
		
		// exists check
		if(ExchangeRate::where(['set_date'=>$date,'currency'=>$request->currency])->exists()){
			return redirect('financial/exchangeRateSetting')->withErrors("資料重複設定");
		}
		
		ExchangeRate::create($request->all());
		
		$row = ExchangeRate::all();
		
		return view('financial.exchangeRate.setting',['data' => $this->resources,'currencys'=> self::CURRENCY_TYPE,'row'=>$row]);
	}
}

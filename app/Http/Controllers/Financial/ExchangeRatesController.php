<?php

namespace App\Http\Controllers\Financial;

use App\ExchangeRate;
use App\Http\Controllers\BaseController;
use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExchangeRatesController extends BaseController
{
    //
	const CURRENCY_TYPE = [
	 'USD','JPY'
	];
	
	public function setting ()
	{
		$fin = new FinancialListController();
		$fin->updateFinancialMoneyReceipt();
		
		if(!in_array(session('userData')['id'],session('role')['admin']['ids'])){
			abort(403);
		};
		$row = ExchangeRate::orderBy('set_date','DESC')->get();
		return view('financial.exchangeRate.setting',['data' => $this->resources,'currencys'=> self::CURRENCY_TYPE,'row'=>$row]);
	}
	
	public function add (Request $request)
	{
		$date = new DateTime($request->set_date.'/01');
		$date = $date->format('Y-m-01');
		$request->request->set('set_date',$date);
		$request->request->set('created_by_erp_user_id',session('userData')['id']);
		
		// exists check
		if(ExchangeRate::where(['set_date'=>$date,'currency'=>$request->currency])->exists()){
			return redirect('financial/exchangeRateSetting')->withErrors("資料重複設定");
		}
		
		ExchangeRate::create($request->all());
		
		$row = ExchangeRate::all();
		
		return view('financial.exchangeRate.setting',['data' => $this->resources,'currencys'=> self::CURRENCY_TYPE,'row'=>$row]);
	}
}

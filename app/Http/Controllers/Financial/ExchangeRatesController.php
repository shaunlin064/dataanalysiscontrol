<?php

namespace App\Http\Controllers\Financial;

use App\ExchangeRate;
use App\FinancialReceipt;
use App\Http\Controllers\BaseController;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExchangeRatesController extends BaseController
{
    //
	protected $policyModel;
	
	const CURRENCY_TYPE = [
	 'USD','JPY'
	];
	
	public function __construct () {
	
		parent::__construct();
	
		$this->policyModel = new ExchangeRate();
	}
	
	public function setting ()
	{
		//permission check
		$this->authorize('view',$this->policyModel);
		
		//$erpUserId = Auth::user()->erp_user_id;
		//abort_if(!in_array($erpUserId,session('role')['admin']['ids']) && !in_array($erpUserId,session('role')['financial']['ids']), 403);
		
		$financialReceiptObj = new FinancialReceipt();
		$financialReceiptObj->updateFinancialMoneyReceipt();
		
		$row = ExchangeRate::orderBy('set_date','DESC')->get();
		return view('financial.exchangeRate.setting',['data' => $this->resources,'currencys'=> self::CURRENCY_TYPE,'row'=>$row]);
	}
	
	public function add (Request $request)
	{
		//permission check
		$this->authorize('update',$this->policyModel);
		
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

<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-07-01
	 * Time: 15:53
	 */
	
	namespace App\Http\Controllers;
	use App\Http\Controllers\ApiController;
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use App\ExchangeRate;
	use DateTime;
	use Illuminate\Support\Str;
	
	class FinancialController extends BaseController
	{
		const CUSTOMER_FIELDS_LOCAL_API_DIFFERENCE = [
		 'accounting_month' => 'year_month',
		 'cam_name' => 'campaign_name',
		 'med_name' => 'media_channel_name',
		 'mtype_name' => 'sell_type_name',
		 't_revenue' => 'income',
		 't_cost' => 'cost'
		];
		
		const CURRENCY_TYPE = [
			'USD','JPY'
		];
		
		
		public function getErpMemberFinancial (Array $userIds,String $dateYearMonth = null ,String  $organizationStr = 'all',$outgroup = null)
		{
			if($dateYearMonth){
				$dateYearMonth = $dateYearMonth;
			}else{
				$dateYearMonth = new \DateTime();
				$dateYearMonth = $dateYearMonth->format('Ym');
			}
			
			$apiObj = new ApiController();
			
			$data = [
			 'token' => env('API_TOKEN'),
			 'action' => 'getUserProfitByCamCue',
			 'data' => [
				'userIds' => $userIds,
				'yearMonthStr' => $dateYearMonth,
				'organizationStr' => $organizationStr,
			  'outgroup' => $outgroup
			 ]
			];
			$url = env('API_GET_MEMBER_FINANCIAL_URL');
			
			$returnData = $apiObj->curlPost(json_encode($data),$url,'json');
			
			
			foreach($returnData as $key => $items){
				
				$items = $this->apiKeyFieldNameChange($items);

				$returnData[$key] = $this->exchangeMoney($items);
				
			}
			
			return $returnData;
		}
		
		public function getUserLatelyProfit ($uid)
		{
			$lastMonth = new \DateTime('last day of last month');
			$lastMonth = $lastMonth->format('Ym');
			$thisMonth = new \DateTime();
			$thisMonth = $thisMonth->format('Ym');
			
			$erpReturnData = $this->getErpMemberFinancial([$uid],'all','all');
			
			$erpReturnData = collect($erpReturnData);
			$erpReturnData = $erpReturnData->groupBy('year_month');
			
			$thisMonthProfit = $erpReturnData->filter(function($item,$key) use($thisMonth) {
					return $key == $thisMonth;
			})->get($thisMonth);
			$thisMonthProfit = empty($thisMonthProfit) ? 0 : $thisMonthProfit->sum('profit');
			
			$lastMonthProfit = $erpReturnData->filter(function($item,$key) use($lastMonth) {
					return $key == $lastMonth;
			})->get($lastMonth);
			$lastMonthProfit = empty($lastMonthProfit) ? 0 : $lastMonthProfit->sum('profit');
			
			$highestProfit = $erpReturnData->map(function($item) {
					return $item->sum('profit');
			});
			$highestProfit = empty($highestProfit) ? 0 : $highestProfit->max();
			
			return [
			 'thisMonthProfit' => $thisMonthProfit,
			 'lastMonthProfit' => $lastMonthProfit,
			 'highestProfit' => $highestProfit
			];
		}
		
		public function exchangeMoney ($items)
		{
			$exchangeRate = ExchangeRate::where(['set_date'=>date('Y-m-01',strtotime($items['year_month'].'01')),'currency'=>$items['currency_id']])->first();
			
			switch($items['currency_id']){
				case 'USD':
					if(empty($exchangeRate)){
						$exchangeRate = 31;
					}else{
						$exchangeRate = $exchangeRate->rate;
					}
					break;
				case 'JPY':
					if(empty($exchangeRate)){
						$exchangeRate = 0.2875;
					}else{
						$exchangeRate = $exchangeRate->rate;
					}
					break;
				default:
					$exchangeRate = 1;
					break;
			}
			
			if( $items['organization'] == 'js' ){
				$exchangeRate = 1;
			}
			
			$items['income'] = round($items['income'] * $exchangeRate);
			$items['cost'] = round($items['cost'] * $exchangeRate);
			$items['profit'] = round($items['profit'] * $exchangeRate);
			
			return $items;
		}
		
		public function apiKeyFieldNameChange ($items)
		{
			foreach(self::CUSTOMER_FIELDS_LOCAL_API_DIFFERENCE as $localKey => $apikey){
				if( in_array($localKey,array_keys($items)) ){
					$items[$apikey] = $items[$localKey];
					unset($items[$localKey]);
				}
			}
			return $items;
		}
		
		public function exchangeRateSetting ()
		{
			
			$row = ExchangeRate::all();
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

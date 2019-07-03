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
	
	class FinancialController extends Controller
	{
		const CUSTOMER_FIELDS_LOCAL_API_DIFFERENCE = [
		 'accounting_month' => 'year_month',
		 'cam_name' => 'campaign_name',
		 'med_name' => 'media_channel_name',
		 'mtype_name' => 'sell_type_name',
		 't_revenue' => 'income',
		 't_cost' => 'cost'
		];
		
		public function getErpMemberFinancial (Array $userIds,String $dateYearMonth = null ,String  $organizationStr = 'all')
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
				'organizationStr' => $organizationStr
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
		
		public function exchangeMoney ($items)
		{
			switch($items['currency_id']){
				case 'USD':
					$exchangeRate = 31;
					break;
				case 'JPY':
					$exchangeRate = 0.2875;
					break;
				default:
					$exchangeRate = 1;
					break;
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
	}

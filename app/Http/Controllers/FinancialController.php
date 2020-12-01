<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-07-01
	 * Time: 15:53
	 */

	namespace App\Http\Controllers;

	use Illuminate\Support\Facades\Http;
	
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
		public function getBalancePayMentData (String $tpye = 'select')
		{
			$data = [
			 'token' => env('API_TOKEN'),
			 'action' => 'balancePayMent',
			 'data' => [
				'selectType' => $tpye
			 ]
			];
			
			return Http::post(env('API_GET_BALANCE_PAYMENT_IDS_URL'),$data)->json();
		}
        public function getErpMemberCancelFinancial (Array $userIds,String $dateYearMonth = null ,String  $organizationStr = 'all',$outgroup = null)
        {

            if(empty($dateYearMonth)){
                $dateYearMonth = new \DateTime();
                $dateYearMonth = $dateYearMonth->format('Ym');
            }

            $data = [
                'token' => env('API_TOKEN'),
                'action' => 'getUserCancelProfitByCamCue',
                'data' => [
                    'userIds' => implode(',',$userIds),
                    'yearMonthStr' => $dateYearMonth,
                    'organizationStr' => $organizationStr,
                    'outgroup' => $outgroup
                ]
            ];

			$returnData = Http::post(env('API_GET_MEMBER_FINANCIAL_URL'),$data)->json();
			
            foreach($returnData as $key => $items){

                $items = $this->apiKeyFieldNameChange($items);

                $returnData[$key] = $items;
            }
            return $returnData;
        }
		public function getErpMemberFinancial (Array $userIds,String $dateYearMonth = null ,String  $organizationStr = 'all',$outgroup = null)
		{

			if(empty($dateYearMonth)){
				$dateYearMonth = new \DateTime();
				$dateYearMonth = $dateYearMonth->format('Ym');
			}

			$data = [
			 'token' => env('API_TOKEN'),
			 'action' => 'getUserProfitByCamCue',
			 'data' => [
				'userIds' => implode(',',$userIds),
				'yearMonthStr' => $dateYearMonth,
				'organizationStr' => $organizationStr,
			  'outgroup' => $outgroup
			 ]
			];
			
			$url = env('API_GET_MEMBER_FINANCIAL_URL');
			$returnData = Http::post($url,$data)->json();
			
			foreach($returnData as $key => $items){
				$items = $this->apiKeyFieldNameChange($items);
				$returnData[$key] = $items;
			}
			return $returnData;
		}

        public function getReciptTimes ($dateMonth = null)
        {
            $data = [
                'token' => env('API_TOKEN'),
                'action' => 'receiptTimes',
                'data' => [
                    'yearMonthStr' => $dateMonth,
                ]
            ];

            $url = env('API_GET_RECEIPT_TIMES_URL');
            return Http::post($url,$data)->json();

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

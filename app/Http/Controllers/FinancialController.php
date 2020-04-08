<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-07-01
	 * Time: 15:53
	 */

	namespace App\Http\Controllers;

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

			$apiObj = new ApiController();

			$data = [
			 'token' => env('API_TOKEN'),
			 'action' => 'balancePayMent',
			 'data' => [
				'selectType' => $tpye
			 ]
			];
			$url = env('API_GET_BALANCE_PAYMENT_IDS_URL');

			$returnData = $apiObj->curlPost(json_encode($data),$url,'json');

			return $returnData;
		}
        public function getErpMemberCancelFinancial (Array $userIds,String $dateYearMonth = null ,String  $organizationStr = 'all',$outgroup = null)
        {

            if(empty($dateYearMonth)){
                $dateYearMonth = new \DateTime();
                $dateYearMonth = $dateYearMonth->format('Ym');
            }

            $apiObj = new ApiController();

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

            $url = env('API_GET_MEMBER_FINANCIAL_URL');

            $returnData = $apiObj->curlPost(json_encode($data),$url,'json');
//            dd($returnData,$url);
            foreach($returnData as $key => $items){

                $items = $this->apiKeyFieldNameChange($items);

                //				$returnData[$key] = $this->exchangeMoney($items);
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

			$apiObj = new ApiController();

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

			$returnData = $apiObj->curlPost(json_encode($data),$url,'json');

			foreach($returnData as $key => $items){

				$items = $this->apiKeyFieldNameChange($items);

//				$returnData[$key] = $this->exchangeMoney($items);
				$returnData[$key] = $items;
			}
			return $returnData;
		}

        public function getReciptTimes ($dateMonth = null)
        {
            $apiObj = new ApiController();

            $data = [
                'token' => env('API_TOKEN'),
                'action' => 'receiptTimes',
                'data' => [
                    'yearMonthStr' => $dateMonth,
                ]
            ];

            $url = env('API_GET_RECEIPT_TIMES_URL');
            return $apiObj->curlPost(json_encode($data),$url,'json');

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

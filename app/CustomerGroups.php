<?php
    /**
     * Created by PhpStorm.
     * User: shaun
     * Date: 2019/12/13
     * Time: 下午3:12
     */
    
    namespace App;
    
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\Http;

    class CustomerGroups
    {
        const CUSTOMER_TYPE = [
            'client' => 1,
            'agency' => 2,
        ];
        
        private $apiData;
        private $cacheKey = 'customerGroups';
        
        public function __construct ()
        {
            $this->apiData = [
                'token' => env('API_TOKEN'),
            ];
        }
        
        public function getCustomerGroupDatas ()
        {
            $returnData = null;
            $checkHasUpdate = $this->getApiUpdateGroup();
            
            /*先確認是否有更新資料 沒有直接取上次cache*/
            if (empty($checkHasUpdate['data'])) {
                $returnData = Cache::store('memcached')->get($this->cacheKey);
            }
            /*如果上次也沒有cache資料 重新call 取全部資料*/
            if (empty($returnData)) {
                try {
                    $returnData = $this->getApiAllGroupData();
                    if ($returnData['status'] == 1) {
                        Cache::forget($this->cacheKey);
                        Cache::store('memcached')->forever($this->cacheKey, $returnData);
                    }else {
                        return '集團資料api錯誤'.$returnData['message'];
                    }
                } catch (Exception $ex) {
                    echo $ex->getMessage();
                }
            }
            
//            if (1) {
//                $returnData['data'] = [
//                    [
//                        "id" => 1,
//                        "name" => "亞力山大集團",
//                        "customer" => [
//                            "agency" => [
//                                219,183,136
//                            ],
//                            "client" => [
//                                227,1656,1437
//                            ]
//                        ]
//                    ],
//                    [
//                        "id" => 2,
//                        "name" => "亞力山小集團",
//                        "customer" => [
//                            "agency" => [
//                                19,259,30
//                            ],
//                            "client" => [
//                                2006,2005,49
//                            ]
//                        ]
//                    ]
//                ];
//            }
            return collect($returnData['data'])->values()->toArray();
        }
        
        public function getApiUpdateGroup (String $dateTimeStr = null)
        {
            /* example $dateTimeStr = "2019-11-12 :00:00:00"*/
            $this->apiData['update_at'] = $dateTimeStr;
            $apiUrl = env('API_GET_GROUP');
	        return Http::post($apiUrl, $this->apiData)->json();
        }
        
        public function getApiAllGroupData ()
        {
            $this->apiData['update_at'] = '2018-01-01 00:00:00';
            $apiUrl = env('API_GET_GROUP');
	        return Http::post($apiUrl, $this->apiData)->json();
        }
        
        public function getApiCustomerGroups ($customerErpId, String $customerType)
        {
            $this->apiData['id'] = $this::CUSTOMER_TYPE[$customerType];
            $apiUrl = env('API_GET_CUSTOMER_GROUP');
	        return Http::post($apiUrl, $this->apiData)->json();
        }
        
        public function getApiGroupCustomer ($customerGrouupId)
        {
            $this->apiData['id'] = $customerGrouupId;
            $apiUrl = env('API_GET_GROUP_CUSTOMER');
	        return Http::post($apiUrl, $this->apiData)->json();
        }
    }

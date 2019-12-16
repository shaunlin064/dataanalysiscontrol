<?php
    /**
     * Created by PhpStorm.
     * User: shaun
     * Date: 2019/12/13
     * Time: 下午3:12
     */
    
    namespace App;
    
    
    use App\Http\Controllers\ApiController;
    use Illuminate\Support\Facades\Cache;
    
    class CustomerGroups
    {
        const CUSTOMER_TYPE = [
            'client' => 1,
            'agency' => 2,
        ];
        private $api;
        private $apiData;
        private $cacheKey = 'customerGroups';
        
        public function __construct ()
        {
            $this->api = new ApiController();
            $this->apiData = [
                'token' => env('API_TOKEN'),
            ];
        }
        
        public function getCustomerGroupDatas ()
        {
            $returnData = null;
            $checkHasUpdate = $this->getApiUpdateGroup();
            
            if (empty($checkHasUpdate)) {
                $returnData = Cache::store('memcached')->get($this->cacheKey);
            }
            
            if (empty($returnData)) {
                try {
                    $returnData = $this->getApiAllGroupData();
                    Cache::forget($this->cacheKey);
                    if ($returnData['status'] == 1) {
                        Cache::store('memcached')->forever($this->cacheKey, $returnData['data']);
                    }
//                    else {
//                        dd($returnData['message']);
//                    }
                } catch (Exception $ex) {
                    echo $ex->getMessage();
                }
            }
            if (1) {
                $returnData = [
                    [
                        "id" => 1,
                        "name" => "亞力山大集團",
                        "customer" => [
                            "agency" => [
                                219,183,136
                            ],
                            "client" => [
                                227,1656,1437
                            ]
                        ]
                    ],
                    [
                        "id" => 2,
                        "name" => "亞力山小集團",
                        "customer" => [
                            "agency" => [
                                19,259,30
                            ],
                            "client" => [
                                2006,2005,49
                            ]
                        ]
                    ]
                ];
            }
            return $returnData;
        }
        
        public function getApiUpdateGroup (String $dateTimeStr = null)
        {
            /* example $dateTimeStr = "2019-11-12 :00:00:00"*/
            $this->apiData['update_at'] = $dateTimeStr;
            $apiUrl = env('API_GET_GROUP');
            
            return $this->api->curlPost(json_encode($this->apiData), $apiUrl, 'json');
        }
        
        public function getApiAllGroupData ()
        {
            $this->apiData['update_at'] = '2018-01-01 00:00:00';
            $apiUrl = env('API_GET_GROUP');
            
            return $this->api->curlPost(json_encode($this->apiData), $apiUrl, 'json');
        }
        
        public function getApiCustomerGroups (Integer $customerErpId, String $customerType)
        {
            $this->apiData['id'] = $this::CUSTOMER_TYPE[$customerType];
            $apiUrl = env('API_GET_CUSTOMER_GROUP');
            
            return $this->api->curlPost(json_encode($this->apiData), $apiUrl);
        }
        
        public function getApiGroupCustomer (Integer $customerGrouupId)
        {
            $this->apiData['id'] = $customerGrouupId;
            $apiUrl = env('API_GET_GROUP_CUSTOMER');
            
            return $this->api->curlPost(json_encode($this->apiData), $apiUrl, 'json');
        }
    }

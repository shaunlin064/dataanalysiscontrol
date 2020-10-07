<?php

    namespace App\Http\Controllers\Financial;

    use App\ExchangeRate;
    use App\ExchangeRatesAll;
    use App\FinancialReceipt;
    use App\Http\Controllers\BaseController;
    use DateTime;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Validator;
    
    class ExchangeRatesController extends BaseController
    {
        //
        const CURRENCY_TYPE = [
            'USD', 'JPY','CNY','HKD','KRW','SGD','USD','GBP','IDR'
        ];
        protected $policyModel;

        public function __construct ()
        {

            parent::__construct();

            $this->policyModel = new ExchangeRate();
      
        }

        public function setting ()
        {
            //permission check
            $this->authorize('view', $this->policyModel);

            $row = ExchangeRate::orderBy('set_date', 'DESC')->get();
            return view('financial.exchangeRate.setting', ['data' => $this->resources, 'currencys' => self::CURRENCY_TYPE, 'row' => $row]);
        }

        public function add (Request $request)
        {
            //permission check
            $this->authorize('update', $this->policyModel);
            $newdata = $request->all();

            $date = new DateTime($request->set_date . '/01');
            $date = $date->format('Y-m-01');


            $erpUserId = Auth::user()->erp_user_id;
            $newdata['set_date'] = $date;
            $newdata['created_by_erp_user_id'] = $erpUserId;

            // exists check
            if (ExchangeRate::where(['set_date' => $date, 'currency' => $newdata['currency']])->exists()) {
                return redirect('financial/exchangeRateSetting')->withErrors("資料重複設定");
            }

            ExchangeRate::create($newdata);

            $row = ExchangeRate::all();

            return view('financial.exchangeRate.setting', ['data' => $this->resources, 'currencys' => self::CURRENCY_TYPE, 'row' => $row]);
        }

        public function view ()
        {
            //permission check
            $this->authorize('view', $this->policyModel);

            $currencies = [
                ['id' => 'USD', 'name' => '美元-USD'],
                ['id' => 'JPY', 'name' => '日幣-JPY'],
                ['id' => 'CNY', 'name' => '人民幣-CNY'],
                ['id' => 'HKD', 'name' => '港元-HKD'],
                ['id' => 'KRW', 'name' => '韓元-KRW'],
                ['id' => 'SGD', 'name' => '新幣-SGD'],
                ['id' => 'GBP', 'name' => '英鎊-GBP'],
                ['id' => 'IDR', 'name' => '印尼盾-IDR'],
            ];

            return view('financial.exchangeRate.view', ['data' => $this->resources, 'currencys' => $currencies]);
        }

        public function getAjaxData (Request $request, $outType = 'echo')
        {

            $tmpExchangeData = ExchangeRatesAll::where(['currency' => $request->currency, 'year_month' => $request->year_month])->first();
            $data = [];
            if($tmpExchangeData){
                $data = collect(json_decode($tmpExchangeData['data']))->sort()->values();

                $exchangeChartData = [
                    'labels' => $data->pluck('0'),
                    'moneyBuy' => $data->pluck('1'),
                    'moneySell' => $data->pluck('2'),
                    'periodBuy' => $data->pluck('3'),
                    'periodSell' => $data->pluck('4')
                ];

            }else{
                $exchangeChartData = [
                    'labels' => [],
                    'moneyBuy' => [],
                    'moneySell' => [],
                    'periodBuy' => [],
                    'periodSell' => []
                ];

            }
            if ($outType == 'echo') {
                echo json_encode(['exchangeChartData' => $exchangeChartData , 'exchangeTableData' => $data]);
            } else {
                return ['exchangeChartData' => $exchangeChartData , 'exchangeTableData' => $data];
            }
        }
        
	    /**
	     * @parmeter $request
	     *  [
	     * 'token'
	     * 'yearMonth' example '202009'
	     *  'currency'  example 'usd']
	     * @param Request $request
	     * @return false|string
	     */
	    public function getExchangeRateAverage ( Request $request ) {

		    $validator = Validator::make($request->all(), [
			        'token' => ['required' ,'max:255',function ($attribute, $value, $fail) {
				        if ($value !== env('API_TOKEN')) {
					        $fail($attribute.' is invalid.');
				        }
			        }],
				 'yearMonth' => 'required|max:255',
				 'currency' => 'required|max:255',
			 ]);
		    $validator->sometimes('currency', 'required', function ($input) {
			    return $input->token !== env('API_TOKEN');
		    });
		     $message = collect([
			    'status'        => 2,
			    'message'       => '',
			    'data'          => []
		    ]);
			 if ( $validator->errors()->any() ) {
				 $message['message'] = $validator->errors();
				 return  $message->toJson();
			 }
		    try {
			    $yearMonth = $request->yearMonth;
			    $currency = $request->currency;
			    $result = ExchangeRatesAll::where(['year_month'=>$yearMonth,'currency'=> strtoupper($currency)])->first();
			    $keyFiled = collect(['date','buy','sell','spot_buy','spot_sell']);
			    $exchangeRate = collect(json_decode($result->data))->map(function($v,$k) use($keyFiled){
				    $combined = $keyFiled->combine($v);
				    return $combined;
			    });
			    $rateAverage = [
				    'year_month' => $yearMonth,
				    'buy'=> round($exchangeRate->average('buy'),2),
				    'sell' => round($exchangeRate->average('sell'),2),
				    'spot_buy' => round($exchangeRate->average('spot_buy'),2),
				    'spot_sell' => round($exchangeRate->average('spot_sell'),2)
			    ];
			    $message['data'] = $rateAverage;
			    $message['status'] = 1;
			    return $message->toJson();
		    } catch (\Exception $ex) {
		        echo $ex->getMessage();
		    }
		   
        }
	
	    /**
	     * @parmeter $request
	     *  [
	     * 'token'
	     * 'yearMonth' example '202009'
	     *  'currency'  example 'usd']
	     * @parmeter Request $request
	     * @return false|string
	     */
	    public function getExchangeRateLastData ( Request $request ) {
		
		    $validator = Validator::make($request->all(), [
			    'token' => ['required' ,'max:255',function ($attribute, $value, $fail) {
				    if ($value !== env('API_TOKEN')) {
					    $fail($attribute.' is invalid.');
				    }
			    }],
			    'yearMonth' => 'required|max:255',
			    'currency' => 'required|max:255',
		    ]);
		    $validator->sometimes('currency', 'required', function ($input) {
			    return $input->token !== env('API_TOKEN');
		    });
		    $message = collect([
			    'status'        => 2,
			    'message'       => '',
			    'data'          => []
		    ]);
		    if ( $validator->errors()->any() ) {
			    $message['message'] = $validator->errors();
			    return  $message->toJson();
		    }
		    try {
			    $jsonResult = ExchangeRatesAll::where(['year_month'=>$request->yearMonth,'currency'=> strtoupper($request->currency)])->first()->data;
			    $resultData = collect(json_decode($jsonResult));
			    $lastDate = $resultData->max(0);
			    $keyFiled = collect(['date','buy','sell','spot_buy','spot_sell']);
			    
			    $exchangeRate =$resultData->map(function($v) use($keyFiled,$lastDate){
				    return $keyFiled->combine($v);
			    })->where('date',$lastDate)->first();
			    
			    $message['data'] = $exchangeRate;
			    $message['status'] = 1;
			    return $message->toJson();
		    } catch (\Exception $ex) {
			    echo $ex->getMessage();
		    }
		
	    }
    }

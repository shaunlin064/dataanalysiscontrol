<?php
    
    namespace App\Http\Controllers\Financial;
    
    use App\ExchangeRate;
    use App\ExchangeRatesAll;
    use App\FinancialReceipt;
    use App\Http\Controllers\BaseController;
    use DateTime;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    
    class ExchangeRatesController extends BaseController
    {
        //
        const CURRENCY_TYPE = [
            'USD', 'JPY'
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
                ['id' => 'USD', 'name' => 'USD'],
                ['id' => 'JPY', 'name' => 'JPY'],
                ['id' => 'CNY', 'name' => 'CNY'],
            ];
 
            return view('financial.exchangeRate.view', ['data' => $this->resources, 'currencys' => $currencies]);
        }
        
        public function getAjaxData (Request $request, $outType = 'echo')
        {
            
            $tmpExchangeData = ExchangeRatesAll::where(['currency' => $request->currency, 'year_month' => $request->year_month])->first();
            $data = collect(json_decode($tmpExchangeData['data']))->sort()->values();

            $exchangeChartData = [
                'labels' => $data->pluck('0'),
                'moneyBuy' => $data->pluck('1'),
                'moneySell' => $data->pluck('2'),
                'periodBuy' => $data->pluck('3'),
                'periodSell' => $data->pluck('4')
            ];

            if ($outType == 'echo') {
                echo json_encode(['exchangeChartData' => $exchangeChartData , 'exchangeTableData' => $data]);
            } else {
                return ['exchangeChartData' => $exchangeChartData , 'exchangeTableData' => $data];
            }
        }
    }

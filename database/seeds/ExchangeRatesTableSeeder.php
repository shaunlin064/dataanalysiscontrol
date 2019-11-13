<?php
	
	use App\ExchangeRate;
	use Illuminate\Database\Seeder;

class ExchangeRatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $created_by_erp_user_id = 157;
	    /*參考資料為 即期賣出匯率*/
	    $datas = [
	     '201804' => [
	      //[
		    // 'currency' => 'CNY',
		    // 'rate' => '4.6441'
	      //],
	      [
		     'currency' => 'JPY',
		     'rate' => '0.271'
	      ],
	      [
		     'currency' => 'USD',
		     'rate' => '29.3375'
	      ]
	     ],
	     '201805' => [
		    //[
		    // 'currency' => 'CNY',
		    // 'rate' => '4.664'
		    //],
		    [
		     'currency' => 'JPY',
		     'rate' => '0.2703'
		    ],
		    [
		     'currency' => 'USD',
		     'rate' => '29.8182'
		    ]
	     ],
	     '201806' => [
		    //[
		    // 'currency' => 'CNY',
		    // 'rate' => '4.6253'
		    //],
		    [
		     'currency' => 'JPY',
		     'rate' => '0.2714'
		    ],
		    [
		     'currency' => 'USD',
		     'rate' => '30.0163'
		    ]
	     ],
	     '201807' => [
		    //[
		    // 'currency' => 'CNY',
		    // 'rate' => '4.5165'
		    //],
		    [
		     'currency' => 'JPY',
		     'rate' => '0.2722'
		    ],
		    [
		     'currency' => 'USD',
		     'rate' => '30.5032'
		    ]
	     ],
	     '201808' => [
		    //[
		    // 'currency' => 'CNY',
		    // 'rate' => '4.4561'
		    //],
		    [
		     'currency' => 'JPY',
		     'rate' => '0.2747'
		    ],
		    [
		     'currency' => 'USD',
		     'rate' => '30.55'
		    ]
	     ],
	     '201809' => [
		    //[
		    // 'currency' => 'CNY',
		    // 'rate' => '4.4565'
		    //],
		    [
		     'currency' => 'JPY',
		     'rate' => '0.2728'
		    ],
		    [
		     'currency' => 'USD',
		     'rate' => '30.6921'
		    ]
	     ],
	     '201810' => [
		    //[
		    // 'currency' => 'CNY',
		    // 'rate' => '4.433'
		    //],
		    [
		     'currency' => 'JPY',
		     'rate' => '0.2718'
		    ],
		    [
		     'currency' => 'USD',
		     'rate' => '30.48'
		    ]
	     ],
	     '201811' => [
		    //[
		    // 'currency' => 'CNY',
		    // 'rate' => '4.4212'
		    //],
		    [
		     'currency' => 'JPY',
		     'rate' => '0.2702'
		    ],
		    [
		     'currency' => 'USD',
		     'rate' => '30.7957'
		    ]
	     ],
	     '201812' => [
	      //[
	      // 'currency' => 'CNY',
		    //  'rate' => '4.4466'
	      //],
	      [
		     'currency' => 'JPY',
		     'rate' => '0.2726'
	      ],
	      [
		     'currency' => 'USD',
		     'rate' => '30.7660'
	      ]
	     ],
	     '201901' => [
		    //[
		    // 'currency' => 'CNY',
		    // 'rate' => '4.5102'
		    //],
		    [
		     'currency' => 'JPY',
		     'rate' => '0.2811'
		    ],
		    [
		     'currency' => 'USD',
		     'rate' => '30.7737'
		    ]
	     ],
	     '201902' => [
		    //[
		    // 'currency' => 'CNY',
		    // 'rate' => '4.5442'
		    //],
		    [
		     'currency' => 'JPY',
		     'rate' => '0.2769'
		    ],
		    [
		     'currency' => 'USD',
		     'rate' => '30.762'
		    ]
	     ],
	     '201903' => [
		    //[
		    // 'currency' => 'CNY',
		    // 'rate' => '4.567'
		    //],
		    [
		     'currency' => 'JPY',
		     'rate' => '0.2756'
		    ],
		    [
		     'currency' => 'USD',
		     'rate' => '30.7995'
		    ]
	     ],
	     '201904' => [
		    //[
		    // 'currency' => 'CNY',
		    // 'rate' => '4.5656'
		    //],
		    [
		     'currency' => 'JPY',
		     'rate' => '0.2745'
		    ],
		    [
		     'currency' => 'USD',
		     'rate' => '30.8025'
		    ]
	     ],
	     '201905' => [
		    //[
		    // 'currency' => 'CNY',
		    // 'rate' => '4.5149'
		    //],
		    [
		     'currency' => 'JPY',
		     'rate' => '0.2822'
		    ],
		    [
		     'currency' => 'USD',
		     'rate' => '31.18954545'
		    ]
	     ],
	     '201906' => [
		    //[
		    // 'currency' => 'CNY',
		    // 'rate' => '4.5068'
		    //],
		    [
		     'currency' => 'JPY',
		     'rate' => '0.2879'
		    ],
		    [
		     'currency' => 'USD',
		     'rate' => '31.2692'
		    ]
	     ],
	     '201907' => [
		    //[
		    // 'currency' => 'CNY',
		    // 'rate' => '4.491'
		    //],
		    [
		     'currency' => 'JPY',
		     'rate' => '0.2852'
		    ],
		    [
		     'currency' => 'USD',
		     'rate' => '31.0261'
		    ]
	     ],
	     '201908' => [
		    //[
		    // 'currency' => 'CNY',
		    // 'rate' => '4.4099'
		    //],
		    [
		     'currency' => 'JPY',
		     'rate' => '0.2934'
		    ],
		    [
		     'currency' => 'USD',
		     'rate' => '31.3468'
		    ]
	     ],
	     '201909' => [
		     //[
		     // 'currency' => 'CNY',
		     // 'rate' => '4.4099'
		     //],
		    [
		     'currency' => 'JPY',
		     'rate' => '0.287825'
		    ],
		    [
		     'currency' => 'USD',
		     'rate' => '31.06925'
		    ]
	     ],
            '201910' => [
                //[
                // 'currency' => 'CNY',
                // 'rate' => '4.4099'
                //],
                [
                    'currency' => 'JPY',
                    'rate' => '0.2803'
                ],
                [
                    'currency' => 'USD',
                    'rate' => '30.44'
                ]
            ],
	    ];
	    
	    foreach( $datas  as $setDate => $data){
		    foreach ($data as $datum) {
			    $date = new DateTime($setDate.'01');
			    $date = $date->format('Y-m-01');
			    $datum['set_date'] = $date;
			    $datum['created_by_erp_user_id'] = $created_by_erp_user_id;
			
			    ExchangeRate::create($datum);
	    	}
	    }
	    
    }
}

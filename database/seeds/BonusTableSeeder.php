<?php
	
	use App\Bonus;
	use App\BonusLevels;
	use Illuminate\Database\Seeder;
	use Illuminate\Http\Request;
	
	class BonusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
	    $bonusLevels = [
	     0 => [
				"achieving_rate" => "30",
				"bonus_rate" => "5",
				'bonus_direct' => 0,
        ],
	     1 => [
		    "achieving_rate" => "50",
		    "bonus_rate" => "7",
		    'bonus_direct' => 0,
	     ],
	     2 => [
		    "achieving_rate" => "80",
		    "bonus_rate" => "9",
		    'bonus_direct' => 0,
	     ],
	     3 => [
		    "achieving_rate" => "100",
		    "bonus_rate" => "9",
		    'bonus_direct' => 15000,
	     ],
	     4 => [
		    "achieving_rate" => "150",
		    "bonus_rate" => "9",
		    'bonus_direct' => 20000,
	     ]
	    ];
	    // anther rule
	    $exileRuleLevels = [
	     0 => [
		    "achieving_rate" => "50",
		    "bonus_rate" => "5",
		    'bonus_direct' => 0,
	     ],
	     1 => [
		    "achieving_rate" => "70",
		    "bonus_rate" => "10",
		    'bonus_direct' => 0,
	     ],
	     2 => [
		    "achieving_rate" => "100",
		    "bonus_rate" => "10",
		    'bonus_direct' => 15000,
	     ],
	     3 => [
		    "achieving_rate" => "150",
		    "bonus_rate" => "10",
		    'bonus_direct' => 20000,
	     ]
	    ];
	    $exileUserId = [133,153,188];
	    $userdata[] = ['set_date'=>'2018-01-01','erp_user_id' => 186,'boundary' => 400000];
	    $userdata[] = ['set_date'=>'2018-01-01','erp_user_id' => 170,'boundary' => 400000];
	    $userdata[] = ['set_date'=>'2018-01-01','erp_user_id' => 131,'boundary' => 500000];
	    $userdata[] = ['set_date'=>'2018-01-01','erp_user_id' => 122,'boundary' => 550000];
	    $userdata[] = ['set_date'=>'2018-01-01','erp_user_id' => 132,'boundary' => 550000];
	    $userdata[] = ['set_date'=>'2018-01-01','erp_user_id' => 136,'boundary' => 550000];
	    $userdata[] = ['set_date'=>'2018-01-01','erp_user_id' => 155,'boundary' => 550000];
	    $userdata[] = ['set_date'=>'2018-01-01','erp_user_id' => 133,'boundary' => 550000];
	    $userdata[] = ['set_date'=>'2018-01-01','erp_user_id' => 181,'boundary' => 550000];
	    $userdata[] = ['set_date'=>'2018-01-01','erp_user_id' => 153,'boundary' => 550000];
	    $userdata[] = ['set_date'=>'2019-09-01','erp_user_id' => 204,'boundary' => 550000];
	    $userdata[] = ['set_date'=>'2018-01-01','erp_user_id' => 84,'boundary' => 600000];
	    $userdata[] = ['set_date'=>'2018-01-01','erp_user_id' => 174,'boundary' => 600000];
	    $userdata[] = ['set_date'=>'2019-08-01','erp_user_id' => 200,'boundary' => 600000];
	    $userdata[] = ['set_date'=>'2019-08-01','erp_user_id' => 201,'boundary' => 600000];
	    $userdata[] = ['set_date'=>'2019-09-01','erp_user_id' => 205,'boundary' => 600000];
	    $userdata[] = ['set_date'=>'2018-01-01','erp_user_id' => 67,'boundary' => 650000];
	    $userdata[] = ['set_date'=>'2018-01-01','erp_user_id' => 188,'boundary' => 700000];
	
	    $nextMonth = date('Y-m-01',strtotime("+1 month"));

	    foreach($userdata as $importData){
		
		    $dateStart = new \DateTime($importData['set_date']);

		    while($nextMonth != $dateStart->format('Y-m-01')) {
		    	
			    $importData['set_date'] = $dateStart->format('Y-m-01');
			    
			    $request = new Request($importData);
			    $bonus = Bonus::create($request->all());
			    $setBonusLevels = $bonusLevels;
			    
					if( $importData['set_date'] >= '2019-07-01' && in_array($importData['erp_user_id'],$exileUserId)){
						$setBonusLevels = $exileRuleLevels;
					};
			    
			    
			    collect($setBonusLevels)->map(function($v) use($bonus){
				    $v['bonus_id'] = $bonus->id;
				    BonusLevels::create($v);
			    });
			    
			    $dateStart = $dateStart->modify('+1 Month');
		    }
	    }
    }
}

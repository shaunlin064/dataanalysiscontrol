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
	    
	    $userdata[] = ['set_date'=>'','user_id' => 131,'boundary' => 500000];
	    $userdata[] = ['set_date'=>'','user_id' => 186,'boundary' => 400000];
	    $userdata[] = ['set_date'=>'','user_id' => 170,'boundary' => 400000];
	    $userdata[] = ['set_date'=>'','user_id' => 122,'boundary' => 550000];
	    $userdata[] = ['set_date'=>'','user_id' => 132,'boundary' => 550000];
	    $userdata[] = ['set_date'=>'','user_id' => 136,'boundary' => 550000];
	    $userdata[] = ['set_date'=>'','user_id' => 155,'boundary' => 550000];
	    $userdata[] = ['set_date'=>'','user_id' => 181,'boundary' => 550000];
	    $userdata[] = ['set_date'=>'','user_id' => 153,'boundary' => 550000];
	    $userdata[] = ['set_date'=>'','user_id' => 84,'boundary' => 600000];
	    $userdata[] = ['set_date'=>'','user_id' => 174,'boundary' => 600000];
	    $userdata[] = ['set_date'=>'','user_id' => 67,'boundary' => 650000];
	    $userdata[] = ['set_date'=>'','user_id' => 188,'boundary' => 700000];
	
	    $nextMonth = date('Y-m-01',strtotime("+1 month"));

	    foreach($userdata as $importData){
		
		    $dateStart = new \DateTime('2018-05-01');

		    while($nextMonth != $dateStart->format('Y-m-01')) {
		    	
			    $importData['set_date'] = $dateStart->format('Y-m-01');
			    
			    $request = new Request($importData);
			    $bonus = Bonus::create($request->all());
			    
			    collect($bonusLevels)->map(function($v) use($bonus){
				    $v['bonus_id'] = $bonus->id;
				    BonusLevels::create($v);
			    });
			    
			    $dateStart = $dateStart->modify('+1 Month');
		    }
	    }
//	    $characterRole = [
//	     [400000]
//	     [500000]
//	     [550000]
//	     [600000]
//	     [650000]
//	     [700000]
//	     [800000]
//	    ];
//	    $specialist
//	    $seniorSpecialist
//      $salesExecutive
//      $deputyManager
//	    $associateDirector
//	    $director
	     
	    
    }
}
<?php
	
	use App\SaleGroups;
	use Illuminate\Database\Seeder;

class SaleGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
	    
	    $saleGroup = [
	     '整合行銷部' => [
	      '2019-10-01'=>[133,153,174,188,200,201,204,205]
	     ],
	     '業務五部' => [
	      '2019-06-01' => [67,132,136,155],
	      '2019-07-01' => [67,122,131,132,136,155],
	      '2019-08-01' => [67,122,131,132,136,155],
	      '2019-09-01' => [67,122,131,132,136,155],
	      '2019-10-01' => [67,131,132,136,155],
	     ],
	     '業務六部' => [
	      '2019-06-01' => [84,156,170,181],
	      '2019-07-01' => [84,170,181,186],
	      '2019-08-01' => [84,170,181,186],
	      '2019-09-01' => [84,170,181,186],
		     '2019-10-01' => [84,170,181,186]
	    ],
	     '離職業務' => [
	      '2019-07-01' =>[96,97,107,156,161,175],
	      '2019-08-01' =>[96,97,107,156,161,175],
	      '2019-09-01' =>[96,97,107,156,161,175],
	      '2019-10-01' =>[96,97,107,122,156,161,175]
	     ],
	    ];
	    $convener = [67,84];
	    $saleBonus = [ [
		    "achieving_rate" => 100,
		    "bonus_rate" => 0,
		    'bonus_direct' => 15000,
	     ],
	     [
		    "achieving_rate" => 150,
		    "bonus_rate" => 0,
		    'bonus_direct' => 20000,
	     ]];
	
	    foreach ($saleGroup as $name => $item){
		
		    $saleGroups = SaleGroups::create(['name' => $name]);
		
		    $dateNow = new \DateTime(date('Ym01'));
		    
		    $date = new \DateTime(date('2017-12-01'));
		    
		    $groupsData = $saleBonus;
		   
		    while($date->format('Y-m-d') != $dateNow->format('Y-m-d')){
			    $setDate = $date->modify('+1Month')->format('Y-m-d');
			    $nowSetDate = [];
			    foreach ($item as $dataSetDate => $uid){
				    if($dataSetDate == $setDate && empty($nowSetDate)) {
					    $nowSetDate = $uid;
				    }
			    	if($dataSetDate > $setDate && empty($nowSetDate)) {
					    $nowSetDate = $uid;
				    }
			    }
			    $userData = array_map(function($v) use($setDate,$convener,$saleGroups){
				
				    $tmp = [
				     'erp_user_id' => $v,
				     'set_date' => $setDate,
				     'is_convener' => in_array($v,$convener) ? 1 : 0
				    ];
				    return $tmp;
			    },$nowSetDate,[$setDate,$convener,$saleGroups]);
			    $saleGroups->groupsUsers()->createMany($userData);
			    
			    $groupsBonus = array_map(function($v) use($setDate){
				    $v['set_date'] = $setDate;
				    return $v;
			    },$groupsData,[$setDate]);
			
			    $saleGroups->groupsBonus()->createMany($groupsBonus);
					  
		    };
	    }
    }
}

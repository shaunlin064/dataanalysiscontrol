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
	     '整合行銷部' => [133,153,174,188,200,201,204,205],
	     '業務五部' => [67,122,131,132,136,155],
	     '業務六部' => [84,181,170,186],
	     '離職業務' => [19,22,27,28,91,96,97,107,108,156,161,175],
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
		
		    $date = new \DateTime(date('Ym01'));
		    $date->modify('+1Month');
		
		    $groupsData = $saleBonus;
		
		    while($date->format('Y-m-d') != '2018-01-01'){
			    $setDate = $date->modify('-1Month')->format('Y-m-d');
			    $userData = array_map(function($v) use($setDate,$convener,$saleGroups){
				    $tmp = [
				     'erp_user_id' => $v,
				     'set_date' => $setDate,
				     'is_convener' => in_array($v,$convener) ? 1 : 0
				    ];
				    return $tmp;
			    },$item,[$setDate,$convener,$saleGroups]);
			    $saleGroups->groupsUsers()->createMany($userData);
			
			    if(isset($groupsData)){
				    $groupsBonus = array_map(function($v) use($setDate){
					    $v['set_date'] = $setDate;
					    return $v;
				    },$groupsData,[$setDate]);
				
				    $saleGroups->groupsBonus()->createMany($groupsBonus);
			    }
		    };
	    }
    }
}

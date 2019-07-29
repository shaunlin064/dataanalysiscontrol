<?php
	
	use App\Menu;
	use App\MenuSub;
	use Illuminate\Database\Seeder;
	
	class MenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
	    $menus = [
	     [
		    "name" => "責任額",
		    "icon" => "fa fa-street-view",
	      "region" => "user",
	      "catalogue" => "bonus.setting",
		    "subMenu" => [
		     [
			    "name" => "設定",
			    "icon" => "fa fa-circle-o",
			    "url" => "bonus.setting.list",
		     ],
		     [
			    "name" => "個人檢視",
			    "icon" => "fa fa-circle-o",
			    "url" => "bonus.setting.view",
		     ]
		    ],
	     ],
	     [
		    "name" => "獎金",
		    "icon" => "fa fa-usd",
	      "region" => "user",
	      "catalogue" => "bonus.review",
		    "subMenu" => [
		     [
			    "name" => "全體列表",
			    "icon" => "fa fa-circle-o",
			    "url" => "bonus.review.list",
		     ],
		     [
			    "name" => "個人檢視",
			    "icon" => "fa fa-circle-o",
			    "url" => "bonus.review.view",
		     ]
		    ],
	     ],
	     [
		    "name" => "財務",
		    "icon" => "fa fa-calculator",
		    "region" => "user",
		    "catalogue" => "financial",
		    "subMenu" => [
		     [
			    "name" => "匯率設定",
			    "icon" => "fa fa-circle-o",
			    "url" => "financial.exchangeRate.setting",
		     ],
		    ],
	     ],
	    ];
	    foreach($menus as $item){
		    $menuItem = ['name'=>$item['name'],'icon'=>$item['icon'],'region' => $item['region'],'catalogue'=>$item['catalogue']];
		    
		    $menu = Menu::create($menuItem);
		    $menu->subMenu()->createMany($item['subMenu']);

		    $menu->push();
		
	    }
    }
}

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
	    ];
	    foreach($menus as $item){
		    $menuItem = ['name'=>$item['name'],'icon'=>$item['icon']];
		    
		    $menu = Menu::create($menuItem);

		    $menu->subMenu()->createMany($item['subMenu']);

		    $menu->push();
		
	    }
    }
}

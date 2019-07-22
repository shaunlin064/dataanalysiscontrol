<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-07-05
	 * Time: 14:47
	 */
	
	namespace App\Http\Controllers\Auth;
	
	
	use App\Http\Controllers\BaseController;
	use App\Menu;
	use Auth;
	
	class Permission extends BaseController
	{
		public $role;
		public $menus;
		public function __construct ()
		{
			$this->menus = Menu::all();
			
			$this->role['admin']['ids'] = [15,17,157,172,179,187];
			$this->role['user']['ids'] = [];
			$this->role['user']['purview']['menus'] = [1,2];
			$this->role['user']['purview']['menu_subs'] = [2,4];
			$this->role['user']['purview']['menu_sub_level2s'] = [];
			
			$uid = session('userData')['user']['id'];
			$collect = collect($this->role);
			$this->userRoleType = in_array($uid,$this->role['admin']['ids']) ? 'admin' : 'user';
			
//			 $collect->map(function($v,$k) use($uid){
//					return  in_array($uid,$v['ids']);
//			})->filter(function ($v,$k){
//				return  $v;
//			});
//			$this->menus->map(function ($v,$k){
//				foreach($this->userRoleType as $userRoleType => $foo){
//					if($userRoleType != 'admin'){
//
//					}
//				}
//				foreach($v->subMenu as $subMenu ){
//					foreach($subMenu->level2 as $level2){
//					}
//				}
//			});

//			dd($this->role);
//			$this->admin = [15,17,157,172,179,187];
		
		}
	}

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
	use Route;
	use Str;
	
	class Permission extends BaseController
	{
		public $role;
		public $menus;
		public function __construct ()
		{
			$region = Str::before(Route::currentRouteName(),'.')  == 'system' ? 'system' : 'user';
			$this->menus = Menu::where('region',$region )->get();
			$uid = session('userData')['id'];
			$this->role['admin']['ids'] = [15,17,157,172,179,187];
			$this->role['convener']['ids'] = [67,84];
			
			$this->role['user']['ids'] = [];
			if(in_array($uid,$this->role['convener']['ids'])){
				$this->role['user']['purview']['menus'] = [1,2,3];
				$this->role['user']['purview']['menu_subs'] = [2,4,6,7];
			}else{
				$this->role['user']['purview']['menus'] = [1,3];
				$this->role['user']['purview']['menu_subs'] = [2,6,7];
			}
			
			$this->role['user']['purview']['menu_sub_level2s'] = [];
			
			
			
			
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
		public function permissionCheck ($id,$uid)
		{
			if(!in_array($uid,$this->role['admin']['ids']) && $id != $uid){
				abort(403);
			};
		}
		
		public function isAdmin($uid){
			if(!in_array($uid,$this->role['admin']['ids'])){
				return false;
			}else{
				return true;
			}
		}
	}

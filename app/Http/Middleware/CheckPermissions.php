<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Auth\Permission;
use Closure;
use Route;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//	    $uid = session('userData')['id'];
	    //check permission
	    $permission = new Permission();
	    
	    $permission->menus->map(function ($menu, $k1) {
//		    $new_class = $menu->subMenu->map(function ($v, $k) use($menu){
//			    if ($v['url'] == Route::currentRouteName()) {
//				    return 'active menu-open';
//			    }
//		    })->reject(function ($v, $k) {
//			    return $v == null;
//		    });
//
//		    $menu['new_class'] = $new_class->first();
		    
		    if(str_is($menu->catalogue.'*',Route::currentRouteName())){
			    $new_class = 'active menu-open';
		    }else{
			    $new_class = null;
		    }
		    $menu['new_class'] = $new_class;
		    return $menu;
	    });
	    /**
	     * TODO 第三層判斷展開menu 增加class name
	     * menus_subs table 需要增加 new class 欄位
	     *
	     * */
//	    $permission = $this->getMenu($permission);
	    
	    //	    if(!in_array($uid,$permission->role->admin)){
//
//	      session(['menus' => $permission->menus ]) ;
//	    }else{
		    session(['menus' =>  $permission->menus,'role' => $permission->role ,'userRoleType' => $permission->userRoleType]) ;
//	    }
	    
        return $next($request);
    }
	
	/**
	 * @param Permission $permission
	 */
	public function getMenu (Permission $permission)
	{
		$permission->menus->map(function ($menu, $k1) {
			$new_class = $menu->subMenu->map(function ($v, $k) {
				if ($v['url'] == Route::currentRouteName()) {
					return 'active menu-open';
				}
			})->reject(function ($v, $k) {
				return $v == null;
			});
			
			$menu['new_class'] = $new_class->first();
			return $menu;
		});
		return $permission;
	}
}

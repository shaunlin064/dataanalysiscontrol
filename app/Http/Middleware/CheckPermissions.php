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
//	    $uid = session('userData')['user']['id'];
	    //check permission
	    $permission = new Permission();
	    /**
	     * TODO 第三層判斷展開menu 增加class name
	     * menus_subs table 需要增加 new class 欄位
	     *
	     * */
	    $permission->menus->map(function($menu,$k1){
		    $new_class = $menu->subMenu->map(function($v,$k){
	    		if( $v['url'] == Route::currentRouteName()){
	    			return 'active menu-open';
			    }
		    })->reject(function($v,$k){
			    return $v == null;
		    });

		    $menu['new_class'] = $new_class->first();
		    return $menu;
	    });
//	    if(!in_array($uid,$permission->role->admin)){
//
//	      session(['menus' => $permission->menus ]) ;
//	    }else{
		    session(['menus' =>  $permission->menus,'role' => $permission->role ,'userRoleType' => $permission->userRoleType]) ;
//	    }
	    
        return $next($request);
    }
}

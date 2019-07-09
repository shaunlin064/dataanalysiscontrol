<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Auth\Permission;
use Closure;

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
	    $uid = session('userData')['user']['id'];
	    //check permission
	    $permission = new Permission();
	    if(!in_array($uid,$permission->admin)){
	    	$tmp = $permission->menus;
	    	unset($tmp[0]['children'][0]); //設定
		    unset($tmp[1]['children'][0]); //全體列表
	      session(['menus' => $tmp ]) ;
	    }else{
		    session(['menus' =>  $permission->menus]) ;
	    }
	    
        return $next($request);
    }
}

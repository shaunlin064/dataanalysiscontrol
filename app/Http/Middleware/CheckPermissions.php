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
//	    $uid = session('userData')['user']['id'];
	    //check permission
	    $permission = new Permission();
	    
//	    if(!in_array($uid,$permission->role->admin)){
//
//	      session(['menus' => $permission->menus ]) ;
//	    }else{
		    session(['menus' =>  $permission->menus,'role' => $permission->role ,'userRoleType' => $permission->userRoleType]) ;
//	    }
	    
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Route;
use Session;

class CheckLogin
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
    	
    	if ( session('userData') == null ){
		    session(['retrunUrl' => $request->getRequestUri()]);
		    
		    return redirect('login');
	    }
        return $next($request);
    }
}

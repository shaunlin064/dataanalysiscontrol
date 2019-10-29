<?php

namespace App\Http\Middleware;

use App\MenuSub;
use Illuminate\Support\Str;
use App\Menu;
use Closure;
use Route;
use Illuminate\Support\Facades\Cache;

class GetMenuList
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
        $urlName = Route::currentRouteName();
        $region = Str::before($urlName,'.')  == 'system' ? 'system' : 'user';

        $menus = Cache::store('memcached')->remember('menu'.$region, (31536000), function () use($region) {
            return Menu::where('region',$region )->get();
        });
        
        $pageNow = MenuSub::where('url',$urlName)->first();
        
        if($pageNow){
            $parentMenuId = $pageNow->menu_id;
            $menus->map(function ($menu) use($parentMenuId){
                if($menu->id == $parentMenuId){
                    $menu['new_class'] =  'active menu-open';
                    return;
                }
            });
        }
        
	    /**
	     * TODO 第三層判斷展開menu 增加class name
	     * menus_subs table 需要增加 new class 欄位
	     *
	     * */
	    session(['menus' =>  $menus ]) ;
	    
        return $next($request);
    }
}

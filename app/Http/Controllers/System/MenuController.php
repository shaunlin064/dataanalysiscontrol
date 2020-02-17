<?php

namespace App\Http\Controllers\System;

use App\Bonus;
use App\Menu;
use App\MenuSub;
use Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
class MenuController extends BaseController
{
    //
    private $menu;
    private $subMenu;
    public function __construct () {
        parent::__construct();
        $this->menu = new Menu();
        $this->menuSub = new MenuSub();
    }
    
    public function list ()
    {
        $listdata = $this->menu->where('id','<>',0)->with('subMenu')->get();
        
        return view('menu.list',['data' => $this->resources,'listdata' => $listdata]);
    }
    
    public function menuPost (Request $request)
    {
        $menudata = $request->all();
        unset($menudata['_token']);
        
        /*
         * 驗證欄位
         */
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'priority' => 'required',
            'region' => 'required|max:255',
            'catalogue' => 'required|max:255',
        ]);
        
        if(isset($menudata['id'])){
            $this->menu->where('id', $menudata['id'])->update($menudata);
        }else{
            $this->menu->fill($menudata)->save();
        }
        
        return redirect('/system/menu/list');
    }
    
    public function menuDelete (Request $request)
    {
        $menudata = $request->all();
        
        unset($menudata['_token']);
        /*
         * 驗證欄位
         */
        $validatedData = $request->validate([
            'id' => 'required',
        ]);
        
        if(isset($menudata['id'])){
            $menus = $this->menu->where('id', $menudata['id']);
            $menus->first()->subMenu->each(function($v){
                $v->delete();
            });
            $menus->delete();
        }
        
        return redirect('/system/menu/list');
    }
    
    public function menuSubPost (Request $request)
    {
        $menudata = $request->all();
        unset($menudata['_token']);
        
        /*
         * 驗證欄位
         */
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'priority' => 'required',
            'url' => 'required|max:255',
        ]);
        
        if(isset($menudata['id'])){
            $this->menuSub->where('id', $menudata['id'])->update($menudata);
        }else{
            $this->menuSub->fill($menudata)->save();
            
        }
        
        return redirect('/system/menu/list');
    }
    
    public function menuSubDelete (Request $request)
    {
        $menudata = $request->all();
        
        unset($menudata['_token']);
        /*
         * 驗證欄位
         */
        $validatedData = $request->validate([
            'id' => 'required',
        ]);
        
        if(isset($menudata['id'])){
            $this->menuSub->where('id', $menudata['id'])->delete();
        }
        
        return redirect('/system/menu/list');
    }
    
}

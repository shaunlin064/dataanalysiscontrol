<?php

namespace App\Http\Controllers;

use App\Bonus;
use App\Menu;
use Illuminate\Http\Request;

class MenuController extends BaseController
{
    //
    public function __construct () {
        
        parent::__construct();
        
    }
    
    public function list ()
    {
        $menuobj = new Menu();
        $listdata = $menuobj->where('id','<>',0)->with('subMenu')->get();
//        $userbonus = $this->where([
//            'erp_user_id' => $erpUserId,
//            'set_date' => $dateYearMonth
//        ])->with('levels')->get()->first()->toArray();
//        dd($listdata[0]->submenu);
//        foreach($listdata as $data){
//           echo $data['name'];
//        }
//        die;
        return view('menu.list',['data' => $this->resources,'listdata' => $listdata]);
    }
    
}

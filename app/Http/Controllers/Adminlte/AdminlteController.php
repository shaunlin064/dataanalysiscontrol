<?php
    /**
     * Created by PhpStorm.
     * User: shaun
     * Date: 2020/2/14
     * Time: 下午4:04
     */
    
    namespace App\Http\Controllers\Adminlte;
    
    
    use App\Http\Controllers\BaseController;

    class AdminlteController extends BaseController
    {
        public function __construct () {
        
            parent::__construct();
        
        }
    
        public function icon ()
        {
            return view('adminlte.icons',['data' => $this->resources]);
        }
    }

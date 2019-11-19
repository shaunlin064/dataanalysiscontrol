<?php

namespace Tests\Feature;

use App\Http\Controllers\FinancialController;
use App\Http\Controllers\UserController;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiErpTest extends TestCase
{
    public function test_erp_login ()
    {
        $erpObj = new \App\Http\Controllers\Auth\AuthCustomerController();

        $request = new \Illuminate\Http\Request($this->userProvide());
    
        $this->assertEquals(1,$erpObj->erpLogin($request)['status'],'登入失敗');
    }
    
    public function userProvide() {
    
        return ['name' => 'guest' , 'password' => ''];
    }
    
    public function test_get_user()
    {
        $userObj = new UserController();
        $userObj->getErpUser();
        $this->assertNotEmpty($userObj->users,'未取到使用者');
        $this->assertNotEmpty($userObj->department,'未取到部門');
    }
    
    public function test_get_financial_list()
    {
        $financial = new FinancialController();
        $this->assertNotEmpty(($financial->getErpMemberFinancial(['all'],'all')),'取財報資料錯誤');
    
    }
    
    public function test_get_balance_payment()
    {
        $financial = new FinancialController();
        $this->assertNotEmpty($financial->getBalancePayMentData('all'),'取財報已收款資料錯誤');
        
    }

}

<?php
    
    namespace Tests\Feature;
    
    use App\Bonus;
    use App\ExchangeRate;
    use App\FinancialList;
    use App\Http\Controllers\Bonus\BonusReachController;
    use App\Http\Controllers\Bonus\SettingController;
    use App\Http\Controllers\Financial\ExchangeRatesController;
    use App\Http\Controllers\Financial\ProvideController;
    use App\Http\Controllers\SaleGroup\SaleGroupController;
    use App\Http\Controllers\System\RoleControl;
    use App\Permission;
    use App\Role;
    use App\SaleGroups;
    use App\SaleGroupsReach;
    use App\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Tests\TestCase;
    
    class CrudDailyRoutineTest extends TestCase
    {
        
        public function test_bonus_new_add ()
        {
            $this->user_acting();
            $bonusSettingObj = new SettingController();
            DB::beginTransaction();
            $this->assertEquals(302, $bonusSettingObj->save(new Request($this->getProvidedBonusData()), '2040-12-01')->status());
            $this->assertTrue(Bonus::where('erp_user_id', $this->getProvidedBonusData()['erp_user_id'])->where('set_date', '2040-12-01')->exists());
            DB::rollBack();
        }
        
        public function user_acting ()
        {
            $user = User::where('name', '=', 'van')->first();
            $this->actingAs($user);
        }
        
        public function getProvidedBonusData (): array
        {
            return [
                "erp_user_id" => "22",
                "boundary" => "10",
                "bonus_levels" => [
                    [
                        "achieving_rate" => "10",
                        "bonus_rate" => "10",
                        "bonus_direct" => "10",
                    ]
                ]
            ];
        }
        
        public function test_add_sale_group ()
        {
            $this->user_acting();
            $saleGroupController = new SaleGroupController();
            DB::beginTransaction();
            $request = new Request($this->getProvidedSaleGroupsData());
            $saleGroupController->post($request);
            $this->assertTrue(SaleGroups::where('name', $request->name)->exists());
            DB::rollBack();
        }
        
        public function getProvidedSaleGroupsData (): array
        {
            return [
                "user_now_select" => "133,153,174,188,200,201,204,205",
                "user_now_select_is_convener" => "133",
                "name" => "test部",
                "sale_group_id" => "0",
                "groupsBonus" => [
                    [
                        "achieving_rate" => "150",
                        "bonus_rate" => "0",
                        "bonus_direct" => "20000",
                    ],
                    [
                        "achieving_rate" => "100",
                        "bonus_rate" => "0",
                        "bonus_direct" => "15000",
                    ]
                ]
            ];
        }
        
        public function test_bonus_reach ()
        {
            $this->user_acting();
            DB::beginTransaction();
            $objcontrol = new BonusReachController();
            $this->assertTrue($objcontrol->update());
            DB::rollBack();
        }
        
        public function test_exchangeRate_add ()
        {
            $this->user_acting();
            $exchangeData = [
                "set_date" => "2040/12",
                "currency" => "USD",
                "rate" => "30.1",
            ];
            DB::beginTransaction();
            $objcontrol = new ExchangeRatesController();
            $objcontrol->add(new Request($exchangeData));
            $this->assertTrue(ExchangeRate::where([
                'set_date' => '2040-12-01',
                'currency' => 'USD',
                'rate' => '30.1'
            ])->exists());
            DB::rollBack();
        }
        
        public function test_provide_bonus ()
        {
            $this->user_acting();
            $data = [
                'provide_bonus' => FinancialList::where('status', 1)->first()->id,
                'provide_sale_groups_bonus' => SaleGroupsReach::where('status', 0)->first()->id
            ];
            $obj = new ProvideController();
            $request = new Request($data);
            
            DB::beginTransaction();
            $obj->post($request);
            $this->assertEquals(2, FinancialList::where('id', $request->provide_bonus)->first()->status);
            $this->assertEquals(1, SaleGroupsReach::where('id', $request->provide_sale_groups_bonus)->first()->status);
            DB::rollBack();
        }
        
        public function test_Role ()
        {
            $this->user_acting();
            
            $data = [
                "name" => "test",
                "label" => "test",
                "permission_ids" => Permission::all()->pluck('id')->toArray()
            ];
            $obj = new RoleControl();
            
            DB::beginTransaction();
            $this->assertEquals(302, $obj->rolePost(newRequest($data))->status());
            DB::rollBack();
            
        }
    
        public function test_role_use ()
        {
            $this->user_acting();
    
            $data = [
                "id" => User::find(1)->id,
                "role_ids" => Role::all()->pluck('id')->toArray()
            ];
            $obj = new RoleControl();
    
            DB::beginTransaction();
            $this->assertEquals(302, $obj->roleUserPost(newRequest($data))->status());
            DB::rollBack();
        }
    }

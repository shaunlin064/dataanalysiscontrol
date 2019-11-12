<?php

use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $permissions = [
            ['name' => 'financial.exchangeRate.setting','label' => '財務_匯率設定'],
            ['name' => 'financial.exchangeRate.view','label' => '財務_匯率檢視'],
            ['name' => 'financial.provide.list','label' => '財務_獎金發放'],
            ['name' => 'saleGroup.setting.list','label' => '招集人_設定'],
            ['name' => 'saleGroup.setting.view','label' => '招集人_個人檢視'],
            ['name' => 'bonus.setting.list','label' => '責任額_設定'],
            ['name' => 'bonus.setting.view','label' => '責任額_檢視'],
            ['name' => 'bonus.review.view','label' => '獎金_業績統計檢視'],
            ['name' => 'financial.provide.view','label' => '獎金_發放檢視'],
            ['name' => 'system.index','label' => '系統_管理'],
            ['name' => 'system.role.list','label' => '系統_角色權限設定'],
            ['name' => 'system.role.user.list','label' => '系統_使用者角色設定']
        ];
        
        collect($permissions)->each(function($permission,$k){
            \App\Permission::create($permission);
        });
    }
}

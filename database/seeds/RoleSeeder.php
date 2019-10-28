<?php
    
    use App\Role;
    use App\Permission;
    use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $roles = [
            ['name' => 'admin','label'=>'管理員'],
            ['name' => 'default','label'=>'預設'],
            ['name' => 'hr','label'=>'HR'],
            ['name' => 'financial','label'=>'財務'],
            ['name' => 'business_director','label'=>'業務總監'],
            ['name' => 'sale_convener','label'=>'業務招集人'],
        ];
        
        collect($roles)->each(function ($role){
            $role = \App\Role::create($role);
            switch($role->name){
                case 'default':
                    $permissions = Permission::whereIn('name',
                        ['financial.exchangeRate.view',
                            'bonus.setting.view',
                            'bonus.review.view',
                            'financial.provide.view'])->get();
                    break;
                case 'admin':
                    $permissions = Permission::all();
                    break;
                case 'financial':
                    $permissions = Permission::whereIn('name',
                        ['financial.exchangeRate.setting'])->get();
                    break;
                case 'hr':
                    $permissions = Permission::whereIn('name',
                        ['saleGroup.setting.list','bonus.setting.list'])->get();
                    break;
                case 'business_director':
                    $permissions = Permission::whereIn('name',
                        ['saleGroup.setting.list','bonus.setting.list','saleGroup.setting.view'])->get();
                    break;
                case 'sale_convener':
                    $permissions = Permission::whereIn('name',
                        ['saleGroup.setting.view'])->get();
                    break;
            }
            $role->permissions()->attach($permissions);
        });
    }
}

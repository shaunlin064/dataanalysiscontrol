<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       $this->call([
        MenuTableSeeder::class,
        ExchangeRatesTableSeeder::class,
        UsersTableSeeder::class,
        BonusTableSeeder::class,
        SaleGroupsSeeder::class,
        SaleGroupsReachsSeeder::class,
        PermissionSeeder::class,
        RoleSeeder::class,
        UserRoleSeeder::class,
       ]);
    }
}

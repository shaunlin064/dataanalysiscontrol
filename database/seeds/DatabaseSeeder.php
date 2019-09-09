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
        BonusTableSeeder::class,
        ExchangeRatesTableSeeder::class,
        SaleGroupsSeeder::class,
        SaleGroupsReachsSeeder::class,
        UsersTableSeeder::class,
       ]);
    }
}

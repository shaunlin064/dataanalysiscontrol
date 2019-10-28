<?php

use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $users = \App\User::all();
        
        
        $users->each(function ($user){
            //set default
            $user->assignRole('default');
            //set admin
            if(in_array($user->name,['van','shaun','alvin'])){
                $user->assignRole('admin');
            }
            //financial
            if(in_array($user->name,['white','parin','mia','ariel.lai'])){
                $user->assignRole('financial');
            }
            //hr
            if(in_array($user->name,['nancy'])){
                $user->assignRole('hr');
            }
            //business_director
            if(in_array($user->name,['johnny','elynn'])){
                $user->assignRole('business_director');
            }
            //sale_convener
            if(in_array($user->name,['kell','hunter'])){
                $user->assignRole('sale_convener');
            }
        });
    }
}

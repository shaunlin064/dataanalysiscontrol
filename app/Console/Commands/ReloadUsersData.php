<?php

namespace App\Console\Commands;

use App\Http\Controllers\UserController;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ReloadUsersData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reload_users_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'reload userdata';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
	    DB::statement('SET FOREIGN_KEY_CHECKS=0');
	    DB::table('users')->truncate();
	    DB::statement('SET FOREIGN_KEY_CHECKS = 1');
	    $erpUserDatas = new UserController();
	    $erpUserDatas->getErpUser();
	    
	    foreach ($erpUserDatas->users as $item){
		    $userObj = User::where('erp_user_id',$item['id'])->first();
		    
		    $item['name'] = $item['account'];
		    
				if(empty($userObj)){
					$request = new Request($item);
					$request->merge(['password'=>Hash::make($request->get('password'))]);
					$request->merge(['erp_user_id'=>$request->id]);
					User::create($request->toArray());
					
				}else{
					$userObj->name = $item['account'];
					$userObj->email = $item['email'];
					$userObj->save();
				}
				
	    }
	
    }
}

<?php
	
	use App\Http\Controllers\UserController;
	use App\User;
	use Illuminate\Database\Seeder;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Hash;
	
	class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $erpUserDatas = new UserController();
	    $erpUserDatas->getErpUser();
	
	    $fillable = [
	     'name', 'email', 'password','erp_user_id'
	    ];
	    DB::statement('SET FOREIGN_KEY_CHECKS=0');
	    DB::table('users')->truncate();
	    DB::statement('SET FOREIGN_KEY_CHECKS = 1');
	    
	    foreach ($erpUserDatas->users as $item){
		
		    $userObj = User::where('erp_user_id',$item['id'])->first();
		    $item['name'] = $item['account'];
		
		    if(empty($userObj)){
			
			    $item = collect($item);
			    $item['password'] = Hash::make($item->get('password'));
			    $item['erp_user_id'] = $item->get('id');
			    $item->map(function($v,$k) use($fillable,&$item){
				    if(!in_array($k,$fillable)){
					    unset($item[$k]);
				    }
			    });
			    User::create($item->toArray());
			
		    }else{
			    $userObj->name = $item['account'];
			    $userObj->email = $item['email'];
			    $userObj->save();
		    }
	    }
    }
}

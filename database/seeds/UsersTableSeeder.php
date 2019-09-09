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
        //
	    DB::statement('SET FOREIGN_KEY_CHECKS=0');
	    User::truncate();
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

<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-07-02
	 * Time: 17:00
	 */
	
	namespace App\Http\Controllers\Auth;
	ini_set('max_execution_time', 600);
	use App\Http\Controllers\ApiController;
	use App\Http\Controllers\Bonus\ReviewController;
	use App\Http\Controllers\UserController;
    use App\Role;
    use Illuminate\Foundation\Auth\AuthenticatesUsers;
	use App\User;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\Redirect;
	use App\Http\Controllers\Help\Crypt;
	use Session;
	use Auth;
	
	class AuthCustomerController
	{
		use AuthenticatesUsers;
		
		public static $encrypt = "FAA2C53CA77AEF2F77C6E3C83C81B798";
		
		public function index (Request $request)
		{
			
			$key = $request->key;
			if( $key == null ){
				return view('auth.login');
			}
			
			$result = explode('_', Crypt::decrypt($key, 'AES-256-CBC'));
			
			if(empty($result[0])){
				return Redirect::route('auth.login');
			}
			
			$newRequest = new Request();
			$newRequest->merge([
			 'name' => $result[0],
			 'password' => $result[1],
				'auto' => 1
			]);
			
			
			$message = self::login($newRequest);
			
			return Redirect::route('index');
			
		}
		
		static function erpLogin (Request $request)
		{
			if(empty($request->auto)){
				$request->password = md5($request->password);
			}
			$apiObj = new ApiController();
			$data = 'username='.$request->name.'&password='.$request->password.'&apikey='.self::$encrypt;
			
			$url = env('API_LOGIN_URL');
			
			return $apiObj->curlPost($data,$url,'form',false);
			
		}
		
		static function dacLogin (Request $request){
			
			$userObj = User::where('name',$request->name)->first();
			
			if(empty($userObj)){
				$request->merge(['password'=>Hash::make($request->get('password'))]);
				$request->merge(['erp_user_id'=>$request->id]);
				
				$userObj = User::create($request->toArray());
				
				// new user add default role
				$roleDefault = Role::where('name','default')->first();
				if($roleDefault){
                    $userObj->roles()->attach($roleDefault);
                }
				
				$userObj->fresh();
			}
//			Auth::attempt($request->only('name', 'password'));
			Auth::loginUsingId($userObj->id);
		}
		
		public function login (Request $request)
		{
			$message = $this->erpLogin($request);
			//
			if($message['status'] != 1){
				return view('handle',['message'=>$message,'returnUrl' => Route('auth.index')]);
			}
			
			$request->merge($message['data']['user']);
			$this->dacLogin($request);
			
			$message = $this->setUserSession($message);
			
			if( !empty(session('retrunUrl')) ){
				$returnUrl = session('retrunUrl');
				session()->forget('retrunUrl');
				return redirect($returnUrl);
			}
			return redirect('/');
		}
		
		public function logout ()
		{
			
			session()->forget('userData');
			return Redirect::route('auth.login');
		}
		
		public function setUserSession ($message)
		{
			$userObj = new UserController();
			$userObj->getErpUser();
			
			session(['department' => $userObj->department]);
			session(['users' => $userObj->users]);
			
			session(['userData'=> $message['data']['user']]);
			
			return $message;
		}
		public function username()
		{
			return 'name';
		}
	}

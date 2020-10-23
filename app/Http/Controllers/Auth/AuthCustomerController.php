<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-07-02
	 * Time: 17:00
	 */

	namespace App\Http\Controllers\Auth;
	use App\Cachekey;
	use App\Http\Controllers\BaseController;
	use App\Http\Controllers\Help\Crypt;
	use App\Role;
	use App\User;
	use Illuminate\Foundation\Auth\AuthenticatesUsers;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Artisan;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\Http;
	use Illuminate\Support\Facades\Redirect;
	use Session;
	
	class AuthCustomerController extends BaseController
	{
		use AuthenticatesUsers;
		
		public function index (Request $request)
		{
			
			$this->resources['cssPath'][] = '/css/login.css';
			
			$key = $request->key;
			if( $key == null ){
				return view('auth.login',['data' => $this->resources]);
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
			
			if(env('APP_DEMO')){
				$credentials = $request->only('name','password');
				
				if (Auth::attempt($credentials)) {
					$user = auth()->user();
					return [
						"status"        => 1,
						"status_string" => "登入成功",
						"message"       => "",
						"data"          => [
							"user"  => [
								"name"          => $user->name,
								"id"            => $user->erp_user_id,
								"email"         => $user->email,
								"department_id" => "1",
								"department"    => 'Demo',
								"created_at"    => "2018-04-23",
							],
							"token" => "=",
						]
					];
				}
			}
			
			$data = [
				'username'=> $request->name,
				'password'=> $request->password,
				'apikey'=> env('API_KEY')
			];
			
			return Http::asForm()->post(env('API_LOGIN_URL'),$data)->json();
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
			return  Redirect::route('bonus.review.view');
		}

		public function logout ()
		{

			session()->forget('userData');

            Auth::logout();

			return Redirect::route('auth.login');
		}

		public function setUserSession ($message)
		{
		    if(!Cachekey::where('type','users')->exists()){
                Artisan::call('cache_erp_datas');
            }

			session(['userData'=> $message['data']['user']]);

			return $message;
		}
		public function username()
		{
			return 'name';
		}
	}

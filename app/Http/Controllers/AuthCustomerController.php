<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-07-02
	 * Time: 17:00
	 */
	
	namespace App\Http\Controllers;
	
	
	use App\Http\Controllers\ApiController;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Redirect;
	use App\Http\Controllers\Help\Crypt;
	use Session;
	
	class AuthCustomerController
	{
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
			 'username' => $result[0],
			 'password' => $result[1],
				'auto' => 1
			]);
			
			
			$message = self::login($newRequest);
			
			return Redirect::route('index');
			
		}
		
		public function login (Request $request)
		{
			if(empty($request->auto)){
				$request->password = md5($request->password);
			}
			$apiObj = new ApiController();
			$data = 'username='.$request->username.'&password='.$request->password.'&apikey='.self::$encrypt;
			
			$url = env('API_LOGIN_URL');
			
			$message = $apiObj->curlPost($data,$url,'form',false);
			
			if($message['status'] != 1){
				return view('handle',['message'=>$message,'returnUrl' => Route('auth.index')]);
			}
			
			session(['userData'=> $message['data']]);
			
			return redirect()->action('IndexController@index');
			
		}
		
		
	}

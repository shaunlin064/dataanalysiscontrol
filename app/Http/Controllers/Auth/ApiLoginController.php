<?php


    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use Auth;
    use Illuminate\Foundation\Auth\AuthenticatesUsers;
    use Illuminate\Http\Request;
    use Illuminate\Validation\ValidationException;
    use Validator;

    class ApiLoginController extends Controller {
        /*
        |--------------------------------------------------------------------------
        | Login Controller
        |--------------------------------------------------------------------------
        |
        | This controller handles authenticating users for the application and
        | redirecting them to your home screen. The controller uses a trait
        | to conveniently provide its functionality to your applications.
        |
        */

        use AuthenticatesUsers;

        /**
         * Where to redirect users after login.
         *
         * @var string
         */
        protected $redirectTo = '/handle';

        /**
         * Create a new controller instance.
         *
         * @return void
         */
        public function __construct () {

            $this->middleware('api')->except('login');

        }

        public function login ( Request $request ) {
            //Validator
            $ruleArr = [
                'name'     => 'required|string',
                'password' => 'required|string',
            ];

            $validator = Validator::make($request->request->all(), $ruleArr);
            if ( $validator->fails() ) {
                $errors = $validator->errors();
                $newErrors['event'] = 'fail';
                $newErrors['message'] = $errors->messages();

                return response()->json($newErrors, 201);
            }
            //如果 user table 已有資料 且密碼無誤 直接登入
            if ( $this->attemptLogin($request) ) {
                $user = $this->guard()->user();
                $user->generateToken();

                return response()->json($user->getOriginal('api_token'));
            }
            //遠端登入
            $message = AuthCustomerController::erpLogin($request);
			
            if ( $message['status'] == 1 ) {
                //				$message['data']['user']['password'] = $request->get('password');
                $request->merge($message['data']['user']);
                //				$request = new Request($message['data']['user']);

                AuthCustomerController::dacLogin($request);

                $user = $this->guard()->user();
                $user->generateToken();
                return response()->json($user->getOriginal('api_token'));
            }

            $errors = [
                "event"   => "fail",
                "message" => ValidationException::withMessages([
                    'name or password' => [ trans('auth.failed') ],
                ])->errors()
            ];

            return response()->json($errors, 201);

        }

        public function logout ( Request $request ) {

            $user = \App\User::where('api_token', $request->api_token)->first();

            if ( $user ) {
                $user->api_token = null;
                $user->save();
                return response()->json([ 'data' => 'success' ], 200);
            } else {
                return response()->json([ 'data' => 'fail' ], 200);
            }

        }

        public function username () {
            return 'name';
        }
    }

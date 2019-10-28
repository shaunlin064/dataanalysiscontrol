<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
	
	use App\User;
	use Illuminate\Support\Facades\Auth;
	use \Illuminate\Support\Facades\Cache;
	use \Illuminate\Support\Facades\Artisan;
	
	/*
	 * 登入系統
	 */
	
	Route::get('/test',function(){
	
		//$role1 = new \App\Role;
		//$role1->name = 'sale_convener';
		//$role1->label = '業務招集人';
		//$role1->save();
		//$role2 = new \App\Role;
		//$role2->name = 'default';
		//$role2->label = '預設';
		//$role2->save();
		$role = \App\Role::find(5);
		//
		$user = \App\User::where('erp_user_id',157)->first();
		//dd(auth()->user()->hasRole('admin'));
		//$user->assignRole($role);
		//dd(User::where('erp_user_id',157)->get()->isAdmin());
		//dd($user->isAdmin());
		//dd($user->hasRole($role));
		////
		//$permission = \App\Permission::find([4]);
		//$permission->map(function($permission,$k) use ( $role ){
		//	$role->givePermissionTo($permission);
		//});
		////
		dd($role,$permission);
		
		Auth::loginUsingId(42);
		return View('welcome');
	});
	
	Route::get('/info',function(){
		phpinfo();
	});
	
	Route::get('/cacheflush',function(){
		dd(Cache::store('memcached')->flush());
	});
	
	Route::group(['namespace' => '\App\Http\Controllers\Auth'], function() {
		Route::get('/login', [
		 'as' => 'auth.index',
		 'uses' => 'AuthCustomerController@index',
		 ]);
		
		Route::post('/login', [
		 'as' => 'auth.login',
		 'uses' => 'AuthCustomerController@login',
		 ]);
		
		Route::any('/logout', [
		 'as' => 'auth.logout',
		 'uses' => 'AuthCustomerController@logout',
		 ]);
	});

	
	Route::group(['middleware' => ['CheckLogin','CheckPermissions'] ], function(){
		/*
		 * index home
		 */
		//Route::get('/',function(){
		//	$user = Auth::user();
		//	dd(Gate::allows('edit-settings',$user));
		//});
		Route::get('/', [
		 'as' => 'index',
		 'uses' => 'IndexController@index'
		]);
		
		
		Route::group(['namespace' => '\App\Http\Controllers\Bonus'], function() {
			//bonus start
			//setting
			Route::prefix('bonus/setting')->group(function(){
			  
					Route::get('/list', [
					 'as' => 'bonus.setting.list',
					 'uses' => 'SettingController@list',
					]);
					Route::get('/add',[
					 'as' => 'bonus.setting.add',
					 'uses' => 'SettingController@add',
					]);
					Route::get('/edit/{id?}',
					 [
						'as' => 'bonus.setting.edit',
						'uses' => 'SettingController@edit',
					 ]);
					Route::get('/view/{id?}', [
					 'as' => 'bonus.setting.view',
					 'uses' => 'SettingController@view',
					]);
					
					Route::any('/save',
					 [
						'as' => 'bonus.setting.save',
						'uses' => 'SettingController@save',
					 ]);
					Route::post('/update',
					 [
						'as' => 'bonus.setting.update',
						'uses' => 'SettingController@update',
					 ]);
				
		});
		
			/*
		 * bonus/review
		 */
			Route::prefix('bonus/review')->group(function(){
				//review
//				Route::get('/list', 'ReviewController@list')->name('bonus.review.list');
//				Route::get('/edit/{id}', 'ReviewController@edit')->name('bonus.review.edit');
				Route::get('/view/{id?}', 'ReviewController@view')->name('bonus.review.view');
				Route::any('/getdata', 'ReviewController@getdata')->name('bonus.review.getdata');
				Route::any('/getAjaxData', 'ReviewController@getAjaxData')->name('bonus.review.getAjaxData');
			});
		});
		
		// financial
		Route::group(['namespace' => '\App\Http\Controllers'], function() {
			Route::prefix('financial')->group(function() {
				Route::group(['namespace' => '\App\Http\Controllers\Financial'], function() {
					
					Route::get('/exchangeRateSetting', 'ExchangeRatesController@setting')->name('financial.exchangeRate.setting');
					Route::post('/add', 'ExchangeRatesController@add')->name('financial.exchangeRate.add');
					
					
					Route::prefix('/provide')->group(function () {
						Route::get('/list', 'ProvideController@list')->name('financial.provide.list');
						Route::get('/view/{id?}', 'ProvideController@view')->name('financial.provide.view');
						Route::post('/getAjaxProvideData', 'ProvideController@getAjaxProvideData')->name('financial.provide.getAjaxProvideData');
						Route::any('/getAllSelectId', 'ProvideController@getAllSelectId')->name('financial.provide.getAllSelectId');
						Route::any('/getAjaxData', 'ProvideController@getAjaxData')->name('financial.provide.getAjaxData');
						Route::any('/ajaxCalculatFinancialBonus', 'ProvideController@ajaxCalculatFinancialBonus')->name('financial.provide.ajaxCalculatFinancialBonus');
						Route::post('/post', 'ProvideController@post')->name('financial.provide.post');
					});
				});
			});
		});
		
		//sale groups setting
		Route::group(['namespace' => '\App\Http\Controllers'], function() {
			Route::prefix('saleGroup/setting')->group(function() {
				Route::group(['namespace' => '\App\Http\Controllers\SaleGroup'], function() {
						Route::get('/list', 'SaleGroupController@list')->name('saleGroup.setting.list');
						Route::get('/add', 'SaleGroupController@add')->name('saleGroup.setting.add');
						Route::get('/edit/{id?}', 'SaleGroupController@edit')->name('saleGroup.setting.edit');
						Route::get('/view/{id?}', 'SaleGroupController@view')->name('saleGroup.setting.view');
						Route::post('/post', 'SaleGroupController@post')->name('saleGroup.setting.post');
				});
			});
		});
		/*
		 * system
		 */
		
		Route::group(['namespace' => '\App\Http\Controllers\System'], function() {
			Route::prefix('system')->group(function(){
				Route::get('/', [
				 'as' => 'system.index',
				 'uses' => 'SystemController@index',
				]);
                Route::get('/roleList', [
                    'as' => 'system.role.list',
                    'uses' => 'RoleControl@roleList',
                ]);
                Route::get('/roleAdd', [
                    'as' => 'system.role.add',
                    'uses' => 'RoleControl@roleAdd',
                ]);
                Route::get('/roleEdit/{id?}', [
                    'as' => 'system.role.edit',
                    'uses' => 'RoleControl@roleEdit',
                ]);
                Route::post('/rolePost', [
                    'as' => 'system.role.post',
                    'uses' => 'RoleControl@rolePost',
                ]);
                
                Route::get('/roleUserList', [
                    'as' => 'system.role.user.list',
                    'uses' => 'RoleControl@roleUserList',
                ]);
//                Route::get('/roleUserAdd', [
//                    'as' => 'system.role.user.add',
//                    'uses' => 'RoleControl@roleUserListAdd',
//                ]);
                Route::get('/roleUserEdit/{id?}', [
                    'as' => 'system.role.user.edit',
                    'uses' => 'RoleControl@roleUserEdit',
                ]);
                Route::post('/roleUserPost', [
                    'as' => 'system.role.user.post',
                    'uses' => 'RoleControl@roleUserPost',
                ]);
			});
		});
	});

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
	});
	
	Route::get('/info',function(){
		phpinfo();
	});
	
	Route::get('/cacheflush',function(){
		dd(Cache::store('memcached')->flush());
	});
	
	Route::group(['namespace' => '\App\Http\Controllers\Auth'], function() {
		Route::get('/login','AuthCustomerController@index')->name('auth.index');
		Route::post('/login','AuthCustomerController@login')->name('auth.login');
		Route::any('/logout','AuthCustomerController@logout')->name('auth.logout');
	});
	
	Route::group(['middleware' => ['CheckLogin','GetMenuList'] ], function(){
		/*
		 * index home
		 */
		Route::get('/','IndexController@index')->name('index');
        
        //bonus start
		Route::group(['namespace' => '\App\Http\Controllers\Bonus'], function() {
            //setting
            Route::prefix('bonus/setting')->group(function(){
                Route::get('/list','SettingController@list')->name('bonus.setting.list');
                Route::get('/add','SettingController@add')->name('bonus.setting.add');
                Route::get('/edit/{bonus?}', 'SettingController@edit')->name('bonus.setting.edit');
                Route::get('/view/{id?}','SettingController@view')->name('bonus.setting.view');
                Route::any('/save', 'SettingController@save')->name('bonus.setting.save');
                Route::post('/update', 'SettingController@update')->name('bonus.setting.update');
            });
			/*
             * bonus/review  ::TODO改名為 financial 比較符合現況
             */
			Route::prefix('bonus/review')->group(function(){
				//review
				Route::get('/view', 'ReviewController@view')->name('bonus.review.view');
				Route::any('/getdata', 'ReviewController@getdata')->name('bonus.review.getdata');
				Route::any('/getAjaxData', 'ReviewController@getAjaxData')->name('bonus.review.getAjaxData');
			});
		});
		
		// financial
		Route::group(['namespace' => '\App\Http\Controllers'], function() {
			Route::prefix('financial')->group(function() {
				Route::group(['namespace' => '\App\Http\Controllers\Financial'], function() {
					/*匯率設定*/
					Route::get('/exchangeRateSetting', 'ExchangeRatesController@setting')->name('financial.exchangeRate.setting');
					Route::post('/add', 'ExchangeRatesController@add')->name('financial.exchangeRate.add');
					
					/*獎金發放*/
					Route::prefix('/provide')->group(function () {
						Route::get('/list', 'ProvideController@list')->name('financial.provide.list');
						Route::get('/view', 'ProvideController@view')->name('financial.provide.view');
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
                    Route::get('/edit/{saleGroup?}', 'SaleGroupController@edit')->name('saleGroup.setting.edit');
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
				Route::get('/','SystemController@index')->name('system.index');
				/*權限角色設定*/
                Route::get('/roleList','RoleControl@roleList')->name('system.role.list');
                Route::get('/roleAdd','RoleControl@roleAdd')->name('system.role.add');
                Route::get('/roleEdit/{role?}','RoleControl@roleEdit')->name('system.role.edit');
                Route::post('/rolePost','RoleControl@rolePost')->name('system.role.post');
                /*使用者角色*/
                Route::get('/roleUserList','RoleControl@roleUserList')->name('system.role.user.list');
                Route::get('/roleUserEdit/{user?}','RoleControl@roleUserEdit')->name('system.role.user.edit');
                Route::post('/roleUserPost','RoleControl@roleUserPost')->name('system.role.user.post');
			});
		});
	});

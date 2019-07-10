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
	
	/*
			 * 登入系統
			 */
	
	Route::group(['namespace' => '\App\Http\Controllers\Auth'], function() {
		Route::get('/login', [
		 'as' => 'auth.index',
		 'uses' => 'AuthCustomerController@index',
		 'parent' => 'auth.index']);
		
		Route::post('/login', [
		 'as' => 'auth.login',
		 'uses' => 'AuthCustomerController@login',
		 'parent' => 'auth.login']);
		
		Route::any('/logout', [
		 'as' => 'auth.logout',
		 'uses' => 'AuthCustomerController@logout',
		 'parent' => 'auth.logout']);
	});
	
	Route::group(['middleware' => ['CheckLogin','CheckPermissions'] ], function(){
		/*
		 * handle page
		 */
		Route::get('/handle', 'Bonus\ReviewController@getdata');
		/*
		 * index home
		 */
		Route::get('/', [
		 'as' => 'index',
		 'uses' => 'IndexController@index'
		]);
		
		
		Route::group(['namespace' => '\App\Http\Controllers\Bonus'], function() {
			//bonus start
			//setting
			Route::prefix('bonus/setting')->group(function(){
				
					Route::get('/list', 'SettingController@list');
					Route::get('/add',[
					 'as' => 'bonus.setting.add',
						'uses' => 'SettingController@add',
					 'parent' => 'bonus.setting.add'
					]);
					Route::get('/edit/{id}',
					 [
						'as' => 'bonus.setting.edit',
						'uses' => 'SettingController@edit',
						'parent' => 'bonus.setting.edit'
					 ]);
					Route::get('/view/{id}', 'SettingController@view');
					Route::any('/save',
					 [
						'as' => 'bonus.setting.save',
						'uses' => 'SettingController@save',
						'parent' => 'bonus.setting.save'
					 ]);
					Route::post('/update',
					 [
						'as' => 'bonus.setting.update',
						'uses' => 'SettingController@update',
						'parent' => 'bonus.setting.update'
					 ]);
				
		});
		
			/*
		 * bonus/review
		 */
			Route::prefix('bonus/review')->group(function(){
				//review
				Route::get('/list', 'ReviewController@list');
				Route::get('/edit/{id}', 'ReviewController@edit');
				Route::get('/view/{id}', 'ReviewController@view');
				Route::any('/getdata', 'ReviewController@getdata');
				
			});
		});
	});
	
	
	Route::get('/import','Bonus\SettingController@importbonusSetting');
//Auth::routes();

//Route::get('/home', 'IndexController@index')->name('home');

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
//	Route::get('/home', 'HomeController@index')->name('home');
	
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
	Route::get('/menu/firstCreate', [
	 'as' => 'menu.firstCreate',
	 'uses' => 'MenuController@firstCreate'
	]);
	
	Route::group(['middleware' => ['CheckLogin','CheckPermissions'] ], function(){
		/*
		 * index home
		 */
		Route::get('/', [
		 'as' => 'index',
		 'uses' => 'IndexController@index'
		]);
		
		/*
		 * menu Post
		 */
		Route::post('/menu/create', [
		 'as' => 'menu.create',
		 'uses' => 'MenuController@create'
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
				Route::get('/list', 'ReviewController@list')->name('bonus.review.list');
				Route::get('/edit/{id}', 'ReviewController@edit')->name('bonus.review.edit');
				Route::get('/view/{id?}', 'ReviewController@view')->name('bonus.review.view');
				Route::any('/getdata', 'ReviewController@getdata')->name('bonus.review.getdata');
				
			});
		});
	});
	
	
	Route::get('/import','Bonus\SettingController@importbonusSetting');
//Auth::routes();

//Route::get('/home', 'IndexController@index')->name('home');

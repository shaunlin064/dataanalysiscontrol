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
		/*
		 * 登入系統
		 */
		Route::get('/login', [
		 'as'=> 'auth.index',
		 'uses'=> 'AuthCustomerController@index' ,
		 'parent'=> 'auth.index']);
		
		Route::post('/login', [
		 'as'=> 'auth.login',
		 'uses'=> 'AuthCustomerController@login' ,
		 'parent'=> 'auth.login']);
		
		
		//bonus start
		//setting
		Route::prefix('bonus/setting')->group(function(){
		
		Route::get('/list', 'Bonus\SettingController@list');
		Route::get('/add', 'Bonus\SettingController@add');
		Route::get('/edit/{id}',
		 [
			'as' => 'bonus.setting.edit',
			'uses' => 'Bonus\SettingController@edit',
			'parent' => 'bonus.setting.edit'
		 ]);
		Route::get('/view/{id}', 'Bonus\SettingController@view');
		Route::any('/save',
		 [
			'as'=>'bonus.setting.save',
			'uses'=>'Bonus\SettingController@save',
			'parent'=> 'bonus.setting.save'
		 ]);
		Route::post('/update',
		 [
			'as'=>'bonus.setting.update',
			'uses'=>'Bonus\SettingController@update',
			'parent'=> 'bonus.setting.update'
		 ]);
	});
	
		/*
	 * bonus/review
	 */
		Route::prefix('bonus/review')->group(function(){
			//review
			Route::get('/list', 'Bonus\ReviewController@list');
			Route::get('/edit/{id}', 'Bonus\ReviewController@edit');
			Route::get('/view/{id}', 'Bonus\ReviewController@view');
			Route::any('/getdata', 'Bonus\ReviewController@getdata');
			
		});
		
	});
	
//Auth::routes();

//Route::get('/home', 'IndexController@index')->name('home');

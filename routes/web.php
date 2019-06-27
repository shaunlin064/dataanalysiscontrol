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

Route::get('/', 'IndexController@index');
//bonus start
 //setting
	Route::get('/bonus/setting/list', 'Bonus\SettingController@list');
	Route::get('/bonus/setting/edit/{id}', 'Bonus\SettingController@edit');
	Route::get('/bonus/setting/view/{id}', 'Bonus\SettingController@view');
	Route::get('/bonus/setting/add', 'Bonus\SettingController@add');
	Route::POST('/bonus/setting/save', 'Bonus\SettingController@save');
 //review
	Route::get('/bonus/review/list', 'Bonus\ReviewController@list');
	Route::get('/bonus/review/edit/{id}', 'Bonus\ReviewController@edit');
	Route::get('/bonus/review/view/{id}', 'Bonus\ReviewController@view');
	
	Route::get('/bonus/review/getdata', 'Bonus\ReviewController@getdata');
//Route::get('/', function () {
//    return view('welcome');
//});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

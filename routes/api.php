<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
	Auth::routes();

	# 登录路由
	Route::namespace('Auth')->group(function () {
		Route::post('login', 'ApiLoginController@login');
		Route::post('logout', 'ApiLoginController@logout');
	});

	#匯率
	Route::namespace('Financial')->group(function () {
		Route::prefix('exchangeRate/')->group(function () {
			Route::post('getExchangeRateAverage', 'ExchangeRatesController@getExchangeRateAverage');
			Route::post('getExchangeRateLastData', 'ExchangeRatesController@getExchangeRateLastData');
		});
	});
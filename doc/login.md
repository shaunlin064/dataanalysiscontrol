# Route


	Route::group([ 'namespace' => '\App\Http\Controllers\Auth' ], function () {
		Route::get('/login', 'AuthCustomerController@index')->name('auth.index');
		Route::post('/login', 'AuthCustomerController@login')->name('auth.login');
		Route::any('/logout', 'AuthCustomerController@logout')->name('auth.logout');
	});
---	
# AuthCustomerController@index : 28
    
    登入頁面
    
# AuthCustomerController@login
    
    * 登入送後端post  
    * dacLogin :98 驗證 是否有該user

# AuthCustomerController@erpLogin : 58

    如果是從後台直接登入 會使用此function 透過 remote login 驗證
# route

	Route::get('/list', 'SaleGroupController@list')->name('saleGroup.setting.list');
	Route::get('/add', 'SaleGroupController@add')->name('saleGroup.setting.add');
	Route::get('/edit/{saleGroup?}', 'SaleGroupController@edit')->name('saleGroup.setting.edit');
	Route::get('/view/{id?}', 'SaleGroupController@view')->name('saleGroup.setting.view');
	Route::post('/post', 'SaleGroupController@post')->name('saleGroup.setting.post');
	
# SaleGroupController@list : 27

	團隊list頁面
	/**
     * @param null
     * @return array
     *  'row'=> Array , // saleGroups data list
     */

# SaleGroupController@add :35

	新增團隊頁面
	/**
     * @param null
     * @return array
     *  'user'=> Array
     */
	
# SaleGroupController@edit : 57

	編輯團隊頁面
    /**
     * @param SaleGroups sale_groups_id
     * @return array
     *  'user'=> Array
     *   'row'=> Array, //saleGroups data
     *   'totalBoundary' => int,
     *   'groupsBonusHistory' => Array,
     *   'userNowSelect' => Array,
     *   'userNowIsConvener' => Arrat,
     *   'nowRate' => int,
     */
	
# SaleGroupController@view :132
	
	user檢視頁面
    /**
     * @param erp_user_id int
     * @return array
     *  'user'=> Array,
     *  'row'=> Array,
     */
	
# SaleGroupController@post :168

	團隊資料更新
    /**
     * @param Request 
     *  $request = [
     *      'set_date' => string,
     *      'groupsBonus' => Array,
     *      'user_now_select_is_convener' => string,
     *      'user_now_select' => string,
     *      'sale_group_id' => int,
     *  ]
     * 
     * @redirect
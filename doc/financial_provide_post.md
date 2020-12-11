# route

	Route::get('/list', 'ProvideController@list')->name('financial.provide.list');

		 
	Route::post('/post', 'ProvideController@post')->name('financial.provide.post');
	

## '/list', 'ProvideController@list' : 40

前端顯示資料:

	bonusList : 可發放獎金list
	saleGroupsReach : 招集人獎金
	allUserName : user list
	bonusBeyondList : 領導獎金
	
	/**
	* @param null
	* @return
	 [
        'saleGroupsTableColumns' => array,
        'bonuslistColumns'       => array,
        'bonusBeyondColumns'     => arrat,
        'saleGroupsReach'        => array,
        'bonusBeyondList'        => array,
        'bonuslist'              => array,
        'allUserName'            => array,
        'total_mondey'           => int,
	 ]
	 **/

table Columns
	各式Columns Config

## '/post', 'ProvideController@post' :230

接收前端post資料 , 更新各項獎金資料status
SaleGroupsBonusBeyond
FinancialList
SaleGroupsReach

    /**
    * @param Request $request
        [
            'provide_bonus' => int,
            'provide_sale_groups_bonus' => int,
            'provide_bonus_beyond' => str
        ]
    * @return 
        [
            'message'   => array,
            'returnUrl' => str
        ]
___


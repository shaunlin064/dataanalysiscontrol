# route

	Route::get('/list', 'SettingController@list')->name('bonus.setting.list');
	Route::get('/add', 'SettingController@add')->name('bonus.setting.add');
	Route::get('/edit/{bonus?}', 'SettingController@edit')->name('bonus.setting.edit');
	Route::get('/view/{id?}', 'SettingController@view')->name('bonus.setting.view');
	Route::any('/save', 'SettingController@save')->name('bonus.setting.save');
	Route::post('/update', 'SettingController@update')->name('bonus.setting.update');

# SettingController@list :49
	
	責任額list頁面
	/**
     * @param null
     * @return array
     *  'listdata' => $listdata // Bonus set_date 現月之資料list
     */
# SettingController@add :36

	新增責任額頁面
	/**
	 * @param null
	 * @return array
	 *  'addUserLists' => $addUserLists, // 可新增的 userList
	 *  'alreadySetUserIds' => $alreadySetUserIds // 已設定的 userList
	 */
	
# SettingController@edit :67

	編輯責任額頁面
    /**
     * @param Bonus id
     * @return array
     *  'row'=> $bonus, // bonus data
     *  'userData' => $userData // user 名稱,團隊名稱, 歷史毛利資料
     *  'userBonusHistory' => $userBonusHistory // bonusHistory 資料
     */
	
# SettingController@view :90

	個人責任額檢視頁面
	/**
     * @param erp_user_id
     * @return array
     *  'userData' => $userData, // user 名稱,團隊名稱, 歷史毛利資料
     *  'userBonusHistory' => $userBonusHistory // bonusHistory 資料
     */
     
# SettingController@update : 142

	儲存責任額資料
	/**
     * @param 
     * Request $request 
     *  ['bonus_id' => int,
     *   'boundary' => int,
     *   'bonus_levels' => [
     *          [
     *              'achieving_rate' => int,
     *              'bonus_rate' => int,
     *              'bonus_direct' => int
     *          ]
     *      ]
     *   ]
     * @redirect
     */
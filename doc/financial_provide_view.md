# route:87

	Route::get('/view', 'ProvideController@view')->name('bonus.provide.view');
	Route::post('/getAjaxProvideData', 'ProvideController@getAjaxProvideData')
		 ->name('financial.provide.getAjaxProvideData');
		 
---

## '/view', 'ProvideController@view' : 118

    取得各種 list , saleGoups,userList,各項table columns

    /**
     * @param null
     * @return array
     * 'provideBonusColumns'    => Array,
     * 'saleGroupsReachColumns' => Array,
     * 'bonusBeyondColumns'     => Array,
     * 'userList'               => Array,
     * 'saleGroups'             => Array,
     */

## '/getAjaxProvideData', 'ProvideController@getAjaxProvideData' : 277

	前端所有更新資料的入口
	
	/**
	* @param Request 
    * [
	    'startDate' => str,
	    'endDate' => str,
	    'saleGroupIds' => array,
	    'userIds' => array
	    ]
	* @return Array
    * [
    *   'provide_groups_list' => Array,
    *   'provide_bonus_list' => Array,
    *   'provide_char_bar_stack' => Array,
    *   'provide_bonus_beyond_list' => Array,
    *   ]
___

# 前端

前端 vue

* select list
	各種select 使用 select2 套件
	vue temple resources/js/components/ ...
	各種select component以下簡稱 vue-select

	vue-select 綁定select2 change trigger updateSelectToVux
	* vuex
	vuex store js path resources/js/components/store/modules/select.js
	
* 圖表與table
   對應使用的 vue component 
   監聽vuec 對應的result資料
   
* 監聽更新資料
	vue-component FinancialProvideAjax.vue
	
	這邊監聽各種條件 如果有異動就觸發更新function getData
	
	更新各種 result資料 各式圖表與table 從而觸發更新
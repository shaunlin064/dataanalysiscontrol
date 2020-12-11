# route : 78


			Route::prefix('bonus/review')->group(function () {
				//review
				Route::get('/view', 'ReviewController@view')->name('bonus.review.view
				Route::any('/getAjaxData', 'ReviewController@getAjaxData')->name('bonus.review.getAjaxData');
			});
			
---

## '/view', 'ReviewController@view' :53

	後端準備 各項list資料

	list : agency , client , companie , medias , salegroups , userList , pmList
	前端table columns 設定
	
	/**
	* @param null
	* @return Array
	    [
        'bonusColumns'                => array,
        'progressColumns'             => array,
        'progressTotalColumns'        => array,
        'groupProgressColumns'        => array,
        'groupProgressTotalColumns'   => array,
        'saleGroups'                  => array,
        'clientList'                  => array,
        'agencyList'                  => array,
        'mediaCompaniesList'          => array,
        'medias'                      => array,
        'userList'                    => array,
        'pmList'                      => array,
        'customerProfitColumns'       => array,
        'mediasProfitColumns'         => array,
        'mediaCompaniesProfitColumns' => array,
        'customerGroupProfitColumns'  => array
	    ]
	**/


## '/getAjaxData',ReviewController@getAjaxData : 229

	前端所有更新資料的入口,
    /**
    * @param Requet 
    * [
            'startDate' => str,
            'endDate' => str,
            'saleGroupIds' => array,
            'userIds' => array,
            'agencyIdArrays' => array,
            'clientIdArrays' => array,
            'mediaCompaniesIdArrays' => array,
            'mediasNameArrays' => array,
            'selectPms' => array,
        ]
    * @return Array
        [
            "bonus_list"                      => array,
            "chart_money_status"              => array,
            "chart_financial_bar"             => array,
            "chart_financial_bar_last_record" => array,
            "bonus_char_bar_stack"            => array,
            'progress_list'                   => array,
            'progress_list_total'             => array,
            'group_progress_list'             => array,
            'group_progress_list_total'       => array,
            'customer_precentage_profit'      => array,
            'customer_profit_data'            => array,
            'medias_profit_data'              => array,
            'media_companies_profit_data'     => array,
            'sale_channel_profit_data'        => array,
            'customer_groups_profit_data'     => array,
            'chart_bar_max_y'                 => int
        ]

---

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
	vue-component BonusReviewAjax.vue
	
	這邊監聽各種條件 如果有異動就觸發更新function getData
	
	更新各種 result資料 各式圖表與table 從而觸發更新



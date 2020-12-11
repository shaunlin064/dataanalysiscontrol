# 匯率檢視

Route::get('/exchangeRateSetting/view','ExchangeRatesController@view')->name('financial.exchangeRate.view');

Route::any('/exchangeRateSetting/getAjaxData','ExchangeRatesController@getAjaxData')->name('financial.exchangeRate.getAjaxData');

## ExchangeRatesController@view :66

	匯率檢視頁面
	/**
     * @param null
     * @return array
     *  'currencys' => Array
     */

## ExchangeRatesController@getAjaxData : 85

	前端取匯率資料 ExchangeRatesAll
	/**
     * @param $request Array
     * @return array
     *  'exchangeChartData' => Array,
     *  'exchangeTableData' => Array,
     */

---

# 前端

	前端vue componet
	
	exchange-chart-line: 匯率圖表
	date-picker : 設定時間區間
	simple-data-table-componet : 匯率dataTable List
	
	





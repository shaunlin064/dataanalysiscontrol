import store from "./store";

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import Vue from 'vue';
import Vuex from 'vuex';


Vue.use(Vuex);

import test from './store';
let store1 =  new Vuex.Store(test);


// jquery
window.$ = window.jQuery = require('jquery');

import DataTable from 'datatables.net-bs4';
window.DataTable = DataTable;

//chart.js
import Chart from 'chart.js';

import moment from 'moment'
window.moment = moment;
//datepicker


import datepicker from '../../public/adminLte_componets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js';
window.datepicker = datepicker;



//select2
import select2 from '../../public/adminLte_componets/select2/dist/js/select2.full.min.js';

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('menu-component', require('./components/MenuBar.vue').default);

Vue.component('bonus-group-component', require('./components/BonusSettingGroup.vue').default);
Vue.component('bonus-form-component', require('./components/BonusSettingForm.vue').default);
Vue.component('bonus-history-component', require('./components/BonusSettingHistory.vue').default);

Vue.component('bonus-review-ajax-component', require('./components/BonusReviewAjax.vue').default);
Vue.component('financial-provide-ajax-component', require('./components/FinancialProvideAjax.vue').default);

Vue.component('profile-component', require('./components/PersonProfile.vue').default);

Vue.component('data-table-component', require('./components/DataTable.vue').default);
Vue.component('date-picker-component', require('./components/Datepicker.vue').default);


//box
Vue.component('box-info-component', require('./components/BoxInfo').default);
Vue.component('box-progress-component', require('./components/BoxProgress').default);

//chart
Vue.component('chart-component', require('./components/Chart').default);
Vue.component('chart-customer-precentage-profit', require('./components/CustomerPrecentagePie').default);
Vue.component('chart-customer-profit-bar', require('./components/CustomerProfitBar').default);
Vue.component('chart-all-year-profit-line', require('./components/AllYearProfit').default);

Vue.component('test-component', require('./components/test.vue').default);
Vue.component('test2-component', require('./components/test2.vue').default);

// saleGroup
Vue.component('sale-group-form-component', require('./components/SaleGroupForm').default);

//simple dataTalbe
Vue.component('simple-data-table-componet',require('./components/SimpleDatatable').default);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
//provide Bonus Table
Vue.component('provide-data-table-component', require('./components/ProvideDataTable').default);

//pagination
Vue.component('pagination-component', require('./components/Pagination').default);

Vue.component('pagination', require('laravel-vue-pagination'));

//date range
Vue.component('date-range', require('./components/DateRange').default);

Vue.component('select2', require('./components/Select2').default);

Vue.component('select2-customer', require('./components/CustomerSelect2').default);

Vue.component('count-total', require('./components/CountTotal').default);

Vue.component('provide-submit', require('./components/ProvideSubmit').default);

Vue.component('select-button-group', require('./components/SelectButtonGroup').default);


//item
Vue.component('load-item', require('./components/LoadItem').default);
const app = new Vue({
    el: '#app',
    store:store1
});

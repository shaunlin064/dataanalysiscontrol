import Vue from 'vue'
import Vuex from 'vuex'
Vue.use(Vuex)
import * as types from './types'
import state from './state'
import mutations from './mutations'
import actions from './actions'

import financial from './modules/financial'
import select from './modules/select'
import chart from './modules/chart'
import dateRange from './modules/date-range'
import exchangeRate from './modules/exchange-rate'
import datePick from './modules/date-pick'
import dataTable from './modules/data-table'
import permission from './modules/permission'

export default new Vuex.Store({
    mutations,
    state,
    actions,
    modules: {
        financial,
        select,
        chart,
        dateRange,
        exchangeRate,
        datePick,
        dataTable,
        permission
    },
    // strict: process.env.NODE_ENV !== 'production'
})





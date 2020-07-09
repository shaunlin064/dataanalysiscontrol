import * as types from '../types';

const state = {
    color: [
        'rgba(255, 99, 132,0.5)',
        'rgba(111,222,170,0.5)',
        'rgba(83,208,185,0.5)',
        'rgba(75, 192, 192,0.5)',
        'rgba(251,171,126,0.5)',
        'rgba(255, 177, 145,0.5)',
        'rgba(255, 183, 167,0.5)',
        'rgba(142,197,252,0.5)',
        'rgba(143, 171, 235,0.5)',
        'rgba(149, 144, 213,0.5)',
        'rgba(255, 99, 132,0.5)',
        'rgba(216,62,100,0.5)',
        'rgba(90,95,116,0.5)',
        'rgba(201, 203, 207,0.5)',
        'rgba(229,210,195,0.5)',
        'rgba(204,176,172,0.5)',
        'rgba(173,145,9,0.5)',
        'rgba(140,122,159,0.5)',
        'rgba(179,138,174,0.5)',
        'rgba(218,155,183,0.5)',
        'rgba(251,162,174,0.5)',
    ],
    group_id_color: {
        0: 'rgba(255, 205, 86,0.5)',
        1: 'rgba(142,197,252,0.5)',
        2: 'rgba(251,171,126,0.5)',
        3: 'rgba(255, 99, 132,0.5)',
        4: 'rgba(255, 159, 64,0.5)',
        5: 'rgba(201, 203, 207,0.5)',
        6: 'rgba(177,12,71,0.5)',
        7: 'rgba(111,222,170,0.5)',
        8: 'rgba(83,208,185,0.5)',
        10: 'rgba(75, 192, 192,0.5)',
        11: 'rgba(251,171,126,0.5)',
        12: 'rgba(255, 177, 145,0.5)',
        13: 'rgba(255, 183, 167,0.5)',
        14: 'rgba(142,197,252,0.5)',
        15: 'rgba(143, 171, 235,0.5)',
        16: 'rgba(149, 144, 213,0.5)',
        17: 'rgba(255, 99, 132,0.5)',
        18: 'rgba(216,62,100,0.5)',
    },
    default_color: {
        red: 'rgba(255, 99, 132,0.5)',
        orange: 'rgba(255, 159, 64,0.5)',
        yellow: 'rgba(255, 205, 86,0.5)',
        green: 'rgba(75, 192, 192,0.5)',
        blue: 'rgba(142,197,252,0.5)',
        purple: 'rgba(153, 102, 255,0.5)',
        grey: 'rgba(201, 203, 207,0.5)'
    },
    month_income: [],
    month_cost: [],
    month_profit: [],
    money_status_paid: 0,
    money_status_unpaid: 0,
    money_status_bonus_paid: 0,
    money_status_bonus_unpaid: 0,
    month_label: [],
    last_record_month_income: [],
    last_record_month_cost: [],
    last_record_month_profit: [],
    last_record_month_label: [],
    bonus_char_bar_stack: [],
    chart_bar_max_y: 0,
    bonus_list: [],
    customer_profit_data: [],
    customer_groups_profit_data: [],
    medias_profit_data: [],
    media_companies_profit_data: [],
    group_progress_list: [],
    group_progress_list_total: [],
    progress_list: [],
    progress_list_total: [],
    exchange_rates_list: [],
    agency_profit: 0,
    client_profit: 0,
    sale_channel_profitData: []
}
const mutations = {
    [types.MUTATE_MONEY_STATUS]: (state, payload) => {
        state.money_status_paid = payload.paid;
        state.money_status_unpaid = payload.unpaid;
        state.money_status_bonus_paid = payload.bPaid;
        state.money_status_bonus_unpaid = payload.bUnPaid;
    },
    [types.MUTATE_MONTH_BALANCE]: (state, payload) => {
        state.month_income = payload.month_income;
        state.month_cost = payload.month_cost;
        state.month_profit = payload.month_profit;
    },
    [types.MUTATE_CHART_DATA]: (state, payload) => {
        state.last_record_month_income = payload.chart_financial_bar_last_record.totalIncome;
        state.last_record_month_cost = payload.chart_financial_bar_last_record.totalCost;
        state.last_record_month_profit = payload.chart_financial_bar_last_record.totalProfit;
        state.last_record_month_label = payload.chart_financial_bar_last_record.labels;

        state.chart_bar_max_y = payload.chart_bar_max_y;
        /*sales list*/
        state.group_progress_list = payload.group_progress_list;
        state.group_progress_list_total = payload.group_progress_list_total;
        state.progress_list = payload.progress_list;
        state.progress_list_total = payload.progress_list_total;
        /*customer char*/
        state.agency_profit = payload.customer_precentage_profit['agency_profit'];
        state.client_profit = payload.customer_precentage_profit['client_profit'];
        state.month_label = payload.customer_precentage_profit['date'];
        state.sale_channel_profitData = payload.sale_channel_profit_data;


        /*bonus_list*/
        state.bonus_list = payload.bonus_list;

        /*bonus_char_bar_stack*/
        state.bonus_char_bar_stack = payload.bonus_char_bar_stack;
    },
    changeCustomerGroupsProfitData(state, payload) {
        state.customer_groups_profit_data = [];
        payload.map((v) => {
            state.customer_groups_profit_data.push(v)
        });
    },
    changeCustomerProfitData(state, payload) {
        state.customer_profit_data = [];
        payload.map((v) => {
            state.customer_profit_data.push(v)
        });
    },
    changeMediasProfitData(state, payload) {
        state.medias_profit_data = [];
        payload.map((v) => {
            state.medias_profit_data.push(v)
        });
    },
    changeMediaCompaniesProfitData(state, payload) {
        state.media_companies_profit_data = [];
        payload.map((v) => {
            state.media_companies_profit_data.push(v)
        });
    },
}

const actions = {
    [types.CHANGE_MONEY_STATUS]: ({commit}, payload) => {
        commit(types.MUTATE_MONEY_STATUS, payload);
    },
    [types.CHANGE_MONTH_BALANCE]: ({commit}, payload) => {
        commit(types.MUTATE_MONTH_BALANCE, payload);
    },
    [types.SET_CHART_DATA]: ({commit}, payload) => {

        commit(types.MUTATE_CHART_DATA, payload);
        /*customer table list*/
        commit('changeCustomerGroupsProfitData', payload.customer_groups_profit_data);
        commit('changeCustomerProfitData', payload.customer_profit_data);
        commit('changeMediasProfitData', payload.medias_profit_data);
        commit('changeMediaCompaniesProfitData', payload.media_companies_profit_data);

    }
}

const getters = {}

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}

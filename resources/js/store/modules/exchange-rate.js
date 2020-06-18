import * as types from '../types';

const state = {
    chart_exchange_line: [],
    exchange_rates_list: [],
    currency: '',
}

const mutations = {
    [types.MUTATE_CHART_EXCHANGE_LINE]: (state, payload)=> {
        state.chart_exchange_line = payload;
    },
    [types.MUTATE_EXCHANGE_RATES_LIST]: (state, payload)=> {
        state.exchange_rates_list = payload;
    },
    [types.MUTATE_CURRENCY]: (state, payload)=> {

        state.currency = payload;
    },
}

const actions = {
    [types.CHANGE_CHART_EXCHANGE_LINE]: ({commit}, payload) => {
        commit(types.MUTATE_CHART_EXCHANGE_LINE, payload);
    },
    [types.CHANGE_EXCHANGE_RATES_LIST]: ({commit}, payload) => {
        commit(types.MUTATE_EXCHANGE_RATES_LIST, payload);
    },
    [types.CHANGE_CURRENCY]: ({commit}, payload) => {
        commit(types.MUTATE_CURRENCY, payload);
    },
}

const getters = {}

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}

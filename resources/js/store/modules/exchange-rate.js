const state = {
    chart_exchange_line: [],
    exchange_rates_list: [],
    currency: '',
}

const mutations = {
    changeChartExchangeLine(state, payload) {
        state.chart_exchange_line = payload;
    },
    changeExchangeRatesList(state, payload) {
        state.exchange_rates_list = payload;
    },
    changeCurrency(state, payload) {
        state.currency = payload;
    },
}

const actions = {
    changeChartExchangeLine({commit}, payload) {
        commit('changeChartExchangeLine',payload);
    },
    changeExchangeRatesList({commit}, payload) {
        commit('changeExchangeRatesList',payload);
    },
    changeCurrency({commit}, payload) {
       commit('changeCurrency',payload);
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

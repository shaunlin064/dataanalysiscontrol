import * as types from '../types';
const state = {
    start_date: '',
    end_date: '',
}

const mutations = {
    [types.MUTATE_DATE_RANGE] : (state, payload) => {
        state.start_date = payload[0];
        state.end_date = payload[1];
    },
}

const actions = {
    [types.CHANGE_DATE_RANGE] : ({commit}, payload) => {
        commit(types.MUTATE_DATE_RANGE, payload)
    },
}

const getters = {

}

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}

import * as types from '../types';
const state = {
    change_date : ''
}

const getters = {

}

const mutations = {
    [types.MUTATE_DATE] : (state, payload) => {
        state.change_date = payload;
    },
}

const actions ={
    [types.CHANGE_DATE] : ({commit}, payload) => {
        commit(types.MUTATE_DATE,payload)
    },
}

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}

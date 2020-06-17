const state = {
    change_date : ''
}

const getters = {

}

const mutations = {
    changeDate(state, payload) {
        state.change_date = payload;
    },
}

const actions ={
    changeDate({commit}, payload) {
        commit('changeDate',payload)
    },
}

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}

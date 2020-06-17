const state = {
    start_date: '',
    end_date: '',
}

const mutations = {
    changeDateRange(state, payload) {
        state.start_date = payload[0];
        state.end_date = payload[1];
    },
}

const actions = {
    changeDateRange({commit}, payload) {
        commit('changeDateRange', payload)
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

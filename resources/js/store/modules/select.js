const state = {
    user_ids: [],
    sale_group_ids: [],
    agency_ids : [],
    client_ids : [],
    media_companies_ids : [],
    medias_names : [],
}

const mutations = {
    changeUserId(state, payload) {
        state.user_ids = payload;
    },
    changeSaleGroupId(state, payload) {
        state.sale_group_ids = payload;
    },
    changeAgencyIds(state, payload) {
        state.agency_ids = payload;
    },
    changeClientIds(state, payload) {
        state.client_ids = payload;
    },
    changeMediaCompaniesIds(state, payload) {
        state.media_companies_ids = payload;
    },
    changeMediasNames(state, payload) {
        state.medias_names = payload;
    },
}

const actions = {
    changeUserId({commit},payload) {
        commit('changeUserId',payload)
    },
    changeSaleGroupId({commit},payload) {
        commit('changeSaleGroupId',payload)
    },
    changeAgencyIds({commit}, payload) {
        commit('changeAgencyIds',payload)
    },
    changeClientIds({commit}, payload) {
        commit('changeClientIds',payload)
    },
    changeMediaCompaniesIds({commit}, payload) {
        commit('changeMediaCompaniesIds',payload)
    },
    changeMediasNames({commit}, payload) {
        commit('changeMediasNames',payload)
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

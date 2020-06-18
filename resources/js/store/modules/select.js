import * as types from '../types';

const state = {
    user_ids: [],
    sale_group_ids: [],
    agency_ids: [],
    client_ids: [],
    media_companies_ids: [],
    medias_names: [],
}

const mutations = {
    [types.MUTATE_USER_ID] : (state, payload) => {
        state.user_ids = payload;
    },
    [types.MUTATE_SALE_GROUP_ID] : (state, payload) => {
        state.sale_group_ids = payload;
    },
    [types.MUTATE_AGENCY_IDS] : (state, payload) => {
        state.agency_ids = payload;
    },
    [types.MUTATE_CLIENT_IDS] : (state, payload) => {
        state.client_ids = payload;
    },
    [types.MUTATE_MEDIA_COMPANIES_IDS] : (state, payload) => {
        state.media_companies_ids = payload;
    },
    [types.MUTATE_MEDIAS_NAMES] : (state, payload) => {
        state.medias_names = payload;
    },
}

const actions = {
    [types.CHANGE_USER_ID] : ({commit}, payload) => {
        commit(types.MUTATE_USER_ID, payload)
    },
    [types.CHANGE_SALE_GROUP_ID] : ({commit}, payload) => {
        commit(types.MUTATE_SALE_GROUP_ID, payload)
    },
    [types.CHANGE_AGENCY_IDS] : ({commit}, payload) => {
        commit(types.MUTATE_AGENCY_IDS, payload)
    },
    [types.CHANGE_CLIENT_IDS] : ({commit}, payload) => {
        commit(types.MUTATE_CLIENT_IDS, payload)
    },
    [types.CHANGE_MEDIA_COMPANIES_IDS] : ({commit}, payload) => {
        commit(types.MUTATE_MEDIA_COMPANIES_IDS, payload)
    },
    [types.CHANGE_MEDIAS_NAMES] : ({commit}, payload) => {
        commit(types.MUTATE_MEDIAS_NAMES, payload)
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

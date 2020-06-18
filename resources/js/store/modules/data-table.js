import * as types from '../types';
const state = {
    table_select: {},
}

const getters = {
    getPermissionTableSelect(state){
        return state.table_select.permissions_table;
    },
}

const mutations = {
    [types.MUTATE_TABLE_SELECT] : (state,payload = []) => {

        if(state.table_select[payload.dom_id] === undefined){
            state.table_select[payload.dom_id] = [];
        }
        payload.select_id.map((v)=>{
            state.table_select[payload.dom_id].push(v)
        })
    },
    [types.MUTATE_PUSH_TABLE_SELECT] : (state,payload) => {
        let index = $.inArray(payload.select_id, state.table_select[payload.dom_id]);
        if(index === -1){
            state.table_select[payload.dom_id].push(payload.select_id);
        }
    },
    [types.MUTATE_SPLICE_TABLE_SELECT] : (state,payload) => {

        let index = $.inArray(payload.select_id, state.table_select[payload.dom_id]);
        while( index !== -1 ){
            state.table_select[payload.dom_id].splice(index, 1);
            index = $.inArray(payload.select_id, state.table_select[payload.dom_id]);
        }
    },
}
const actions = {
    [types.SET_TABLE_SELECT] : ({commit},payload) => {
        commit(types.MUTATE_TABLE_SELECT,payload)
    },
    [types.PUSH_TABLE_SELECT] : ({commit},payload) => {
        commit(types.MUTATE_PUSH_TABLE_SELECT,payload)
    },
    [types.SPLICE_TABLE_SELECT] : ({commit},payload) => {
        commit(types.MUTATE_SPLICE_TABLE_SELECT,payload)
    },
}

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}

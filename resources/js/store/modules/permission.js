import * as types from '../types';
const state = {
    permission_data : [],
    permission_class_data : []
}

const getters = {

}

const mutations = {
    [types.MUTATE_PERMISSION_DATA] : (state,payload) => {
        state.permission_data = [];
        payload.map((v)=>{state.permission_data.push(v)} );
    },
    [types.MUTATE_PERMISSION_CLASS_DATA] : (state,payload) => {
        state.permission_class_data = [];
        payload.map((v)=>{state.permission_class_data.push(v)} );
    }
}

const actions = {
    [types.CHANGE_PERMISSION_DATA]:({commit},payload)=>{
        commit(types.MUTATE_PERMISSION_DATA,payload);
    },
    [types.CHANGE_PERMISSION_CLASS_DATA]:({commit},payload)=>{
        commit(types.MUTATE_PERMISSION_CLASS_DATA,payload);
    }
}



export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}

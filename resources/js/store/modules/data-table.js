const state = {
    table_select: {},
}

const getters = {
    getPermissionTableSelect(state){
        return state.table_select.permissions_table;
    },
}

const mutations = {
    setTableSelect(state,payload = []){
        if(state.table_select[payload.dom_id] === undefined){
            state.table_select[payload.dom_id] = [];
        }
        payload.select_ids.map((v)=>{
            state.table_select[payload.dom_id].push(v)
        })
    },
    pushTableSelect(state,payload){

        let index = $.inArray(payload.select_id, state.table_select[payload.dom_id]);
        if(index === -1){
            state.table_select[payload.dom_id].push(payload.select_id);
        }
    },
    spliceTableSelect(state,payload){
        let index = $.inArray(payload.select_id, state.table_select[payload.dom_id]);
        while( index !== -1 ){
            state.table_select[payload.dom_id].splice(index, 1);
            index = $.inArray(payload.select_id, state.table_select[payload.dom_id]);
        }
    },
}
const actions = {
    setTableSelect({commit},payload){
        commit('setTableSelect',payload)
    },
    pushTableSelect({commit},payload){
        commit('pushTableSelect',payload)
    },
    spliceTableSelect({commit},payload){
        commit('spliceTableSelect',payload)
    },
}

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}

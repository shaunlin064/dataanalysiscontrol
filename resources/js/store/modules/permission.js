const state = {
    permission_data : [],
    permission_class_data : []
}

const getters = {

}

const mutations = {
    changePermissionData(state,payload){
        state.permission_data = [];
        payload.map((v)=>{state.permission_data.push(v)} );
    },
    changePermissionClassData(state,payload){
        state.permission_class_data = [];
        payload.map((v)=>{state.permission_class_data.push(v)} );
    }
}

const actions = {
    changePermissionData({commit},payload){
        commit('changePermissionData',payload);
    },
    changePermissionClassData({commit},payload){
        commit('changePermissionClassData',payload);
    }
}



export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}

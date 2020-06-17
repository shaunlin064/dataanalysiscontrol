const state = {
    bonus_total_money: 0,
    sale_group_total_money: 0,
    provide_bonus_list: [],
    provide_groups_list: [],
    provide_total_money: 0,
    provide_statistics_list: {
        'user': {},
        'group': {},
    },
    provide_char_bar_stack: {},
    provide_money: 0,
}
const mutations = {
    changeProvideStatisticsList(state, payload) {
        state.provide_statistics_list['user'] = payload['user'];
        state.provide_statistics_list['group'] = payload['group'];
    },
    changeProvideTotalMoney(state, payload) {
        state.provide_total_money = payload;
    },
    increaseProvideTotalMoney(state,payload){
      state.provide_total_money += payload;
    },
    decreaseProvideTotalMoney(state,payload){
        state.provide_total_money += payload;
    },
    changeBonusTotalMoney(state, payload) {
        state.bonus_total_money = payload;
    },
    changeSaleGroupTotalMoney(state, payload){
        state.sale_group_total_money = payload;
    },
    changeProvideBonusList(state,payload){
        state.provide_bonus_list = payload;
    },
    changeProvideGroupsList(state,payload){
        state.provide_groups_list = payload;
    },
    changeProvideCharBarStack(state,payload){
        state.provide_char_bar_stack = payload;
    },
    sortProvideStatisticsList(state){
        // let entries = Object.entries(state.provide_statistics_list.user); //把 obj 拆成 array
        // entries = entries.map( ( a, b) =>{
        //     // if(a.group_id < b.group_id){
        //     //     return 1;
        //     // }
        //     // if(a.group_id > b.group_id){
        //     //     return -1;
        //     // }
        //     // return 0;
        //     return a[1];
        // });
        // state.provide_statistics_list.user  = Object.fromEntries(entries); //把array 轉回 obj
        // console.log(state.provide_statistics_list.user);
        // state.provide_statistics_list.group = state.provide_statistics_list.group.sort(function (a, b) {
        //     return a.group_id < b.group_id ? 1 : -1;
        // });
        //
        state.provide_statistics_list.user = getSort(state.provide_statistics_list.user,'group_id');
        state.provide_statistics_list.group = getSort(state.provide_statistics_list.group,'group_id');

    },
    setStatistics(state,payload){
            // /*user*/
            if (state.provide_statistics_list['user'][payload.user_name] === undefined) {
                state.provide_statistics_list['user'][payload.user_name] = {};
                state.provide_statistics_list['user'][payload.user_name].money = 0;
                state.provide_statistics_list['user'][payload.user_name].group_id = 0;
                state.provide_statistics_list['user'][payload.user_name].user_name = payload.user_name;
            }
            /*sale group*/
            let groupName = payload.sale_group_name ? payload.sale_group_name : payload.group_name;
            if (state.provide_statistics_list['group'][groupName] === undefined) {
                state.provide_statistics_list['group'][groupName] = {};
                state.provide_statistics_list['group'][groupName]['money'] = 0;
                state.provide_statistics_list['group'][groupName]['group_id'] = 0;
                state.provide_statistics_list['group'][groupName]['groupName'] = groupName;
            }
            // /*區分獎金檢視與獎金發放 */
            let separateDate = payload.provide_set_date !== undefined ? payload.provide_set_date : payload.set_date;
            if (state.provide_char_bar_stack[separateDate] === undefined) {
                state.provide_char_bar_stack[separateDate] = {};
            }
            if (state.all_user_name !== undefined) {
                let allName = state.all_user_name;
                let vue = state;
                Object.keys(allName).forEach(k => {
                    if (vue.provide_char_bar_stack[separateDate][k] === undefined) {
                        vue.provide_char_bar_stack[separateDate][k] = {};
                        vue.provide_char_bar_stack[separateDate][k]['provide_money'] = 0;
                        vue.provide_char_bar_stack[separateDate][k]['erp_user_id'] = allName[k];
                    }
                }, vue, separateDate);
            }
            if (state.provide_char_bar_stack[separateDate][payload.user_name] === undefined) {
                state.provide_char_bar_stack[separateDate][payload.user_name] = {};
                state.provide_char_bar_stack[separateDate][payload.user_name]['provide_money'] = 0;
            }
            state.provide_char_bar_stack[separateDate][payload.user_name]['erp_user_id'] = payload.user !== undefined ? payload.user.erp_user_id : payload.sale_user.erp_user_id;

    },
    selectData(state, payload) {

        let thisSelectMoney = payload.data.provide_money;
        let groupName = payload.data.sale_group_name ? payload.data.sale_group_name : payload.data.group_name;
        let groupId = payload.data.sale_groups_id !== undefined ? payload.data.sale_groups_id : payload.data.sale_groups.sale_groups_id;
        let userName = payload.data.user_name ? payload.data.user_name : '';
        let set_date = payload.fromPage === 'provide/view' ?  payload.data.provide_set_date : payload.data.set_date;
        if(payload.type !== 'select'){
            thisSelectMoney = thisSelectMoney * -1;
        }
        state.provide_total_money += thisSelectMoney;

        state.provide_statistics_list['user'][userName]['money'] += thisSelectMoney;
        state.provide_statistics_list['user'][userName]['group_id'] = groupId;
        state.provide_statistics_list['group'][groupName]['money'] += thisSelectMoney;
        state.provide_statistics_list['group'][groupName]['group_id'] = groupId;
        state.provide_char_bar_stack[set_date][userName]['provide_money'] += thisSelectMoney;
        if (state.provide_statistics_list['user'][userName]['money'] === 0) {
            delete state.provide_statistics_list['user'][userName];
        }
        if (state.provide_statistics_list['group'][groupName]['money'] === 0) {
            delete state.provide_statistics_list['group'][groupName];
        }
        if(state.provide_char_bar_stack[set_date][userName]['provide_money'] === 0){
            delete state.provide_char_bar_stack[set_date][userName];
        }
        if(Object.keys(state.provide_char_bar_stack[set_date]).length === 0){
            delete state.provide_char_bar_stack[set_date];
        }
    }
}
const actions = {
    changeProvideStatisticsList({commit}, payload) {
        commit('changeProvideStatisticsList', payload)
    },
    changeProvideTotalMoney({commit}, payload) {
        commit('changeProvideTotalMoney', payload)
    },
    increaseProvideTotalMoney({commit},payload){
        commit('increaseProvideTotalMoney', payload)
    },
    decreaseProvideTotalMoney({commit},payload){
        commit('decreaseProvideTotalMoney', payload)
    },
    changeBonusTotalMoney({commit}, payload){
        commit('changeBonusTotalMoney',payload)
    },
    changeSaleGroupTotalMoney({commit}, payload){
        commit('changeSaleGroupTotalMoney',payload)
    },
    changeProvideBonusList({commit}, payload){
        payload.map((v)=>{
            commit('setStatistics',v);
        });
        commit('sortProvideStatisticsList');
        commit('changeProvideBonusList',payload)
    },
    changeProvideGroupsList({commit}, payload){
        payload.map((v)=>{
                commit('setStatistics',v);
        });
        commit('sortProvideStatisticsList');
        commit('changeProvideGroupsList',payload)
    },
    setStatistics({commit}, payload) {
        commit('setStatistics',payload);
    },
    selectData({commit}, payload) {
       commit('selectData',payload);
    },
    sortProvideStatisticsList({commit}, payload) {
        commit('sortProvideStatisticsList',payload);
    },
    changeProvideCharBarStack({commit}, payload){
        commit('changeProvideCharBarStack',payload)
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



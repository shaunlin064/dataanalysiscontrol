import * as types from '../types';

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
    [types.MUTATE_PROVIDE_STATISTICS_LIST] : (state, payload) => {
        state.provide_statistics_list['user'] = payload['user'];
        state.provide_statistics_list['group'] = payload['group'];
    },
    [types.MUTATE_PROVIDE_TOTAL_MONEY] : (state, payload) => {
        state.provide_total_money = payload;
    },
    [types.MUTATE_BONUS_TOTAL_MONEY] : (state, payload) => {
        state.bonus_total_money = payload;
    },
    [types.MUTATE_SALE_GROUP_TOTAL_MONEY] : (state, payload) => {
        state.sale_group_total_money = payload;
    },
    [types.MUTATE_PROVIDE_BONUS_LIST] : (state,payload) => {
        state.provide_bonus_list = payload;
    },
    [types.MUTATE_PROVIDE_GROUPS_LIST](state,payload){
        state.provide_groups_list = payload;
    },
    [types.MUTATE_PROVIDE_CHAR_BAR_STACK] : (state,payload) => {
        state.provide_char_bar_stack = payload;
    },
    [types.MUTATE_SORT_PROVIDE_STATISTICS_LIST] : (state) =>{
        state.provide_statistics_list.user = getSort(state.provide_statistics_list.user,'group_id');
        state.provide_statistics_list.group = getSort(state.provide_statistics_list.group,'group_id');
    },
    [types.MUTATE_RESET_PROVIDE_CHAR_BAR_STACK] : (state) => {
        state.provide_char_bar_stack = {};
    },
    [types.MUTATE_STATISTICS] : (state,payload) => {
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
    [types.MUTATE_SELECT_DATA] : (state, payload) => {

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
    [types.SET_PROVIDE_AJAX_DATA] : ({commit}, payload) => {
        commit(types.MUTATE_RESET_PROVIDE_CHAR_BAR_STACK);
        commit(types.MUTATE_PROVIDE_STATISTICS_LIST, payload.provideStatisticsList);
        commit(types.MUTATE_PROVIDE_TOTAL_MONEY, payload.provideTotalMoney);
        commit(types.MUTATE_BONUS_TOTAL_MONEY, payload.bonusTotalMoney);
        commit(types.MUTATE_SALE_GROUP_TOTAL_MONEY,payload.saleGroupTotalMoney);

        payload.provideBonusList.map((v)=>{
            commit(types.MUTATE_STATISTICS,v);
        });
        payload.provideGroupsList.map((v) => {
            commit(types.MUTATE_STATISTICS,v);
        });

        commit(types.MUTATE_PROVIDE_BONUS_LIST,payload.provideBonusList);
        commit(types.MUTATE_PROVIDE_GROUPS_LIST, payload.provideGroupsList);
        // commit(types.MUTATE_PROVIDE_CHAR_BAR_STACK,payload.provideCharBarStack);
        commit(types.MUTATE_SORT_PROVIDE_STATISTICS_LIST);
    },
    [types.SET_STATISTICS] : ({commit}, payload) => {
        commit(types.MUTATE_STATISTICS,payload);
    },
    [types.SELECT_DATA] : ({commit}, payload) => {
       commit(types.MUTATE_SELECT_DATA,payload);
    },
    [types.SORT_PROVIDE_STATISTICS_LIST]({commit}, payload) {
        commit(types.MUTATE_SORT_PROVIDE_STATISTICS_LIST,payload);
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



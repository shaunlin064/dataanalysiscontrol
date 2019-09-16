export default {
    state: {
        change_date: '',
        table_select: [],
        user_id: 0,
        profit: 0,
        bonus_rate: 0,
        bonus_next_amount: 0,
        bonus_next_percentage: 0,
        bonus_direct:0,
        money_status_paid: 0,
        money_status_unpaid: 0,
        money_status_bonus_paid: 0,
        money_status_bonus_unpaid: 0,
        month_income: 0,
        month_cost: 0,
    },
    getters:{
        getTableSelect(state){
            return state.table_select;
        },
    },
    actions:{
        saveName({commit}, msg) {
            commit('saveMsg', msg) // 提交到mutations中處理
        },
    },
    mutations: {
        changeDate(state,value) {
            state.change_date = value;
        },
        tableSelect(state,value) {
            state.table_select = value;
        },
        changeBox(state,arg){
            switch(arg.field){
                case 'profit':
                    state.profit = arg.value;
                    break;
                case 'bonus_rate':
                    state.bonus_rate = arg.value;
                    break;
                case 'bonus_next_amount':
                    state.bonus_next_amount = arg.value;
                    break;
                case 'bonus_next_percentage':
                    state.bonus_next_percentage = arg.value;
                    break;
                case 'bonus_direct':
                    state.bonus_direct = arg.value;
            }
        },
        changeMoneyStatus(state,arg){
            state.money_status_paid = arg.paid;
            state.money_status_unpaid = arg.unpaid;
            state.money_status_bonus_paid = arg.bPaid;
            state.money_status_bonus_unpaid = arg.bUnPaid;
        },
        changeMonthBalancen(state,arg){
            state.month_income = arg.month_income;
            state.month_cost = arg.month_cost;
        },
        changeUserId(state,value){
            state.user_id = value;
        }
    }
};

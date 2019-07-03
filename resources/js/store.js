export default {
    state: {
        change_date: '',
        user_id: 0,
        profit: 0,
        bonus_rate: 0,
        bonus_next_amount: 0,
        bonus_next_percentage: 0,
        money_status_paid: 0,
        money_status_unpaid: 0,
        money_status_bonus_paid: 0,
        money_status_bonus_unpaid: 0,
        month_income: 0,
        month_cost: 0,
    },
    getters:{

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

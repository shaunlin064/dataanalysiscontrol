export default {
    state: {
        change_date: '',
        profit: 0,
        bounce_rate: 0,
        bounce_next_amount: 0,
        bounce_next_percentage: 0,
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
            state.profit = arg.profit;
            state.bounce_rate = arg.bounce_rate;
            state.bounce_next_amount = arg.bounce_next_amount;
            state.bounce_next_percentage = arg.bounce_next_percentage;
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
        }
    }
};

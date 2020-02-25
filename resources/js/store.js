let date = new Date();
date = `${date.getFullYear()}-${('0'+(date.getMonth()+1)).substring(0,2)}-01`;
export default {
    state: {
        change_date: '',
        start_date: '',
        end_date:'',
        table_select: [],
        user_ids: [],
        sale_group_ids:[],
        profit: 0,
        total_money: 0,
        bonus_total_money : 0,
        provide_total_money : 0,
        sale_group_total_money: 0,
        bonus_list: [],
        bonus_rate: 0,
        bonus_next_amount: 0,
        bonus_next_percentage: 0,
        bonus_direct:0,
        money_status_paid: 0,
        money_status_unpaid: 0,
        money_status_bonus_paid: 0,
        money_status_bonus_unpaid: 0,
        month_income: [],
        month_cost: [],
        month_profit: [],
        month_label: [],
        last_record_month_income: [],
        last_record_month_cost: [],
        last_record_month_profit: [],
        last_record_month_label: [],
        loading:false,
        agency_ids: [],
        client_ids: [],
        media_companies_ids: [],
        medias_names: [],
        agency_profit: [],
        client_profit: [],
        sale_channel_profitData: [],
        customer_profit_data: [],
        customer_groups_profit_data: [],
        media_companies_profit_data: [],
        medias_profit_data: [],
        progress_list: [],
        group_progress_list: [],
        provide_bonus_list: [],
        provide_groups_list: [],
        chart_bar_max_y: 0,
        permission_data:[],
        permission_class_data:[]
    },
    getters:{
        getTableSelect(state){
            return state.table_select;
        }
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
        changeDateRange(state,value){
            state.start_date = value[0];
            state.end_date = value[1];
        },
        tableSelect(state,value) {
            state.table_select = value;
        },
        changeTotalMoney(state,value) {
            state.total_money = value;
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
            state.month_profit = arg.month_profit;
        },
        changeUserId(state,value){
            state.user_ids = value;
        },
        changeSaleGroupId(state,value){
            state.sale_group_ids = value;
        },
        changeAgencyIds(state,value){
            state.agency_ids = value;
        },
        changeClientIds(state,value){
            state.client_ids = value;
        },
        changeMediaCompaniesIds(state,value){
            state.media_companies_ids = value;
        },
        changeMediasNames(state,value){
            state.medias_names = value;
        },
    }
};

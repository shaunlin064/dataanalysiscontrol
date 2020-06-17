<template>

</template>

<script>
    import {mapState, mapMutations, mapActions, mapGetters} from 'vuex';

    export default {
        name: "FinancialProvideAjaxCenter",
        props: {
            csrf: String,
        },
        data() {
            return {
                ajax_url : '/financial/provide/getAjaxProvideData',
                evTimeStamp : 0,
            }
        },
        computed: {
            ...mapState('financial',['bonus_total_money','sale_group_total_money','provide_bonus_list','provide_groups_list','provide_total_money','provide_statistics_list','provide_char_bar_stack']),
            ...mapState('select',['user_ids', 'sale_group_ids',]),
            ...mapState('dateRange',['start_date', 'end_date']),
            ...mapState(['loading']),
            // ...mapState(['start_date', 'end_date', 'user_ids', 'sale_group_ids', 'loading','bonus_total_money','sale_group_total_money','provide_bonus_list','provide_groups_list','provide_total_money','provide_statistics_list','provide_char_bar_stack']),
        },
        methods: {
            tableClear: function () {
                $('table').map(function (e, v) {
                    $.fn.dataTable.Api(v).clear();
                    $.fn.dataTable.Api(v).draw();
                });
            },
            chartClear(){
                bus.$emit('chartClear');
                bus.$emit('chartStackClear');
            },
            getData() {
                var now = +new Date();
                if (now - this.evTimeStamp < 10) {
                    return;
                }
                this.evTimeStamp = now;

                let data = {
                    _token: this.csrf,
                    startDate: this.start_date,
                    endDate: this.end_date,
                    saleGroupIds: this.sale_group_ids,
                    userIds: this.user_ids,
                };

                if ((data.saleGroupIds == '' && data.userIds == '') || data._token === undefined) {
                    return false;
                }


                this.$store.dispatch('changeLoadingStatus',true);
                this.tableClear();
                this.chartClear();

                axios.post(this.ajax_url, data).then(
                    response => {

                        this.$store.dispatch('changeLoadingStatus',false);

                        let bonus_total_money = parseInt(0);
                        let sale_group_total_money = parseInt(0);

                        if (response.data) {

                            response.data.provide_bonus_list.map(function (v) {
                                bonus_total_money += parseInt(v.provide_money);
                            });

                            response.data.provide_groups_list.map(function (v) {
                                sale_group_total_money += parseInt(v.provide_money);
                            });
                            // if(this.$store.state.bonus_total_money !== bonus_total_money ||
                            // this.$store.state.sale_group_total_money !== sale_group_total_money ||
                            // this.$store.state.provide_bonus_list.length !==  response.data.provide_bonus_list.length ||
                            // this.$store.state.provide_groups_list.length !==  response.data.provide_groups_list.length){

                            //     this.$store.state.provide_statistics_list['user'] = [];
                            //     this.$store.state.provide_statistics_list['group'] = [];
                            // this.$store.state.provide_total_money = 0;
                            // this.$store.state.bonus_total_money = bonus_total_money;
                            // this.$store.state.sale_group_total_money = sale_group_total_money;
                            // this.$store.state.provide_bonus_list =  response.data.provide_bonus_list;
                            // this.$store.state.provide_groups_list =  response.data.provide_groups_list;
                            // this.$store.state.provide_char_bar_stack = response.data.provide_char_bar_stack;

                                this.$store.dispatch('financial/changeProvideStatisticsList' ,{user:{},group:{}})
                                this.$store.dispatch('financial/changeProvideTotalMoney',0);
                                this.$store.dispatch('financial/changeBonusTotalMoney',bonus_total_money);
                                this.$store.dispatch('financial/changeSaleGroupTotalMoney',sale_group_total_money)
                                this.$store.dispatch('financial/changeProvideBonusList',response.data.provide_bonus_list);
                                this.$store.dispatch('financial/changeProvideGroupsList',response.data.provide_groups_list);
                                this.$store.dispatch('financial/changeProvideCharBarStack',response.data.provide_char_bar_stack);
                                // this.$store.dispatch('financial/changeProvideTotalMoney',bonus_total_money+sale_group_total_money);
                                response.data.provide_bonus_list.map(v=>{
                                    this.$store.dispatch('financial/setStatistics',v);
                                    this.$store.dispatch('financial/selectData',{data:v,type:'select',fromPage:'provide/view'});
                                });

                            }
                        // }
                    },
                    err => {
                        reject(err);
                    }
                );
            },
        },
        mounted: function () {
        },
        watch: {
            start_date: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== undefined) {
                        this.getData();
                    }
                }
            },
            end_date: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== undefined) {
                        this.getData();
                    }
                }
            },
            user_ids: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== undefined) {
                        this.getData();
                    }
                }
            },
            sale_group_ids: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== undefined) {
                        this.getData();
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>

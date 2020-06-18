<template>

</template>

<script>
    // import { reactive } from '@vue/composition-api';
    import {mapActions, mapState , mapGetters} from 'vuex';
    import * as types from "../store/types";

    export default {
        name: "FinancialProvideAjaxCenter",
        props: {
            csrf: String,
        },
        // setup(props) {
        //     const ajax_url = '/financial/provide/getAjaxProvideData';
        //     let evTimeStamp = reactive({
        //         time:0
        //     });
        //     // let evTimeStamp = ref(0);
        //     const tableClear = () => {
        //         $('table').map(function (e, v) {
        //             $.fn.dataTable.Api(v).clear();
        //             $.fn.dataTable.Api(v).draw();
        //         });
        //     };
        //     const chartClear = () => {
        //         bus.$emit('chartClear');
        //         bus.$emit('chartStackClear');
        //     };
        //     const getData = () => {
        //         var now = +new Date();
        //         if (now - evTimeStamp.time < 10) {
        //             return;
        //         }
        //         evTimeStamp.time = now;
        //
        //         let data = {
        //             _token: props.csrf,
        //             startDate: this.start_date,
        //             endDate: this.end_date,
        //             saleGroupIds: this.sale_group_ids,
        //             userIds: this.user_ids,
        //         };
        //
        //         if ((data.saleGroupIds == '' && data.userIds == '') || data._token === undefined) {
        //             return false;
        //         }
        //
        //         this[types.CHANGE_LOADING_STATUS](true);
        //         this.tableClear();
        //         this.chartClear();
        //
        //         axios.post(this.ajax_url, data).then(
        //             response => {
        //                 this[types.CHANGE_LOADING_STATUS](false);
        //
        //                 let bonus_total_money = parseInt(0);
        //                 let sale_group_total_money = parseInt(0);
        //
        //                 if (response.data) {
        //
        //                     response.data.provide_bonus_list.map(function (v) {
        //                         bonus_total_money += parseInt(v.provide_money);
        //                     });
        //
        //                     response.data.provide_groups_list.map(function (v) {
        //                         sale_group_total_money += parseInt(v.provide_money);
        //                     });
        //
        //                     this.$store.dispatch('financial/changeProvideStatisticsList', {user: {}, group: {}})
        //                     this.$store.dispatch('financial/changeProvideTotalMoney', 0);
        //                     this.$store.dispatch('financial/changeBonusTotalMoney', bonus_total_money);
        //                     this.$store.dispatch('financial/changeSaleGroupTotalMoney', sale_group_total_money)
        //                     this.$store.dispatch('financial/changeProvideBonusList', response.data.provide_bonus_list);
        //                     this.$store.dispatch('financial/changeProvideGroupsList', response.data.provide_groups_list);
        //                     this.$store.dispatch('financial/changeProvideCharBarStack', response.data.provide_char_bar_stack);
        //                     response.data.provide_bonus_list.map(v => {
        //                         this.$store.dispatch('financial/setStatistics', v);
        //                         this.$store.dispatch('financial/selectData', {
        //                             data: v,
        //                             type: 'select',
        //                             fromPage: 'provide/view'
        //                         });
        //                     });
        //
        //                 }
        //             },
        //             err => {
        //                 reject(err);
        //             }
        //         );
        //     };
        //
        //     return {
        //         ajax_url,
        //         evTimeStamp,
        //         tableClear,
        //         chartClear,
        //         getData
        //     };
        // },
        data() {
            return {
                ajax_url: '/financial/provide/getAjaxProvideData',
                evTimeStamp: 0,
            }
        },
        computed: {
            ...mapState('financial', ['bonus_total_money', 'sale_group_total_money', 'provide_bonus_list', 'provide_groups_list', 'provide_total_money', 'provide_statistics_list', 'provide_char_bar_stack']),
            ...mapState('select', ['user_ids', 'sale_group_ids',]),
            ...mapState('dateRange', ['start_date', 'end_date']),
            ...mapState(['loading']),
        },
        methods: {
            ...mapActions([types.CHANGE_LOADING_STATUS]),
            ...mapActions('financial', [types.SET_PROVIDE_AJAX_DATA,types.SET_STATISTICS,types.SELECT_DATA,types.SORT_PROVIDE_STATISTICS_LIST]),
            tableClear() {
                $('table').map(function (e, v) {
                    $.fn.dataTable.Api(v).clear();
                    $.fn.dataTable.Api(v).draw();
                });
            },
            chartClear() {
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

                this[types.CHANGE_LOADING_STATUS](true);
                this.tableClear();
                this.chartClear();
                axios.post(this.ajax_url, data).then(
                    response => {
                        this[types.CHANGE_LOADING_STATUS](false);

                        let bonus_total_money = parseInt(0);
                        let sale_group_total_money = parseInt(0);

                        if (response.data) {

                            response.data.provide_bonus_list.map(function (v) {
                                bonus_total_money += parseInt(v.provide_money);
                            });

                            response.data.provide_groups_list.map(function (v) {
                                sale_group_total_money += parseInt(v.provide_money);
                            });
                            let payload = {
                                provideStatisticsList: {user: {}, group: {}},
                                provideTotalMoney: 0,
                                bonusTotalMoney: bonus_total_money,
                                saleGroupTotalMoney: sale_group_total_money,
                                provideBonusList: response.data.provide_bonus_list,
                                provideGroupsList: response.data.provide_groups_list,
                                provideCharBarStack: response.data.provide_char_bar_stack,
                            }
                            this[types.SET_PROVIDE_AJAX_DATA](payload);
                            response.data.provide_bonus_list.map(v => {
                                this[types.SET_STATISTICS](v);
                                this[types.SELECT_DATA]({
                                    data: v,
                                    type: 'select',
                                    fromPage: 'provide/view'
                                });
                            });

                        }
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

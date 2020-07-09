<template>
</template>

<script>
    import {mapActions, mapState} from 'vuex';
    import * as types from '../store/types';

    export default {
        name: "BonusReviewAjaxCenter",
        props: {
            csrf: String,
        },
        data() {
            return {
                ajax_url: '/bonus/review/getAjaxData',
                evTimeStamp: 0,
            }
        },
        computed: {
            ...mapState(['loading']),
            ...mapState('dateRange', ['start_date', 'end_date']),
            ...mapState('select', ['user_ids', 'sale_group_ids', 'agency_ids', 'client_ids', 'media_companies_ids', 'medias_names']),
            ...mapState('chart', ['bonus_char_bar_stack']),

        },
        methods: {
            ...mapActions([types.CHANGE_LOADING_STATUS]),
            ...mapActions('chart', [
                types.CHANGE_MONEY_STATUS,
                types.CHANGE_MONTH_BALANCE,
                types.SET_CHART_DATA
            ]),
            tableClear: function () {
                $('table').map(function (e, v) {
                    $.fn.dataTable.Api(v).draw();
                    $.fn.dataTable.Api(v).clear();
                });
            },
            getData() {
                var now = +new Date();
                if (now - this.evTimeStamp < 100) {
                    return;
                }
                this.evTimeStamp = now;

                let data = {
                    _token: this.csrf,
                    startDate: this.start_date,
                    endDate: this.end_date,
                    saleGroupIds: this.sale_group_ids,
                    userIds: this.user_ids,
                    agencyIdArrays: this.agency_ids,
                    clientIdArrays: this.client_ids,
                    mediaCompaniesIdArrays: this.media_companies_ids,
                    mediasNameArrays: this.medias_names,
                };

                if ((data.saleGroupIds == '' && data.userIds == '') || data._token === undefined) {
                    return false;
                }

                this[types.CHANGE_LOADING_STATUS](true);
                this.tableClear();

                axios.post(this.ajax_url, data).then(
                    response => {
                        this[types.CHANGE_LOADING_STATUS](false);
                        if (response.data) {
                            /*sales char*/
                            this[types.CHANGE_MONEY_STATUS]({
                                paid: response.data.chart_money_status.paid,
                                unpaid: response.data.chart_money_status.unpaid
                            });
                            this[types.CHANGE_MONTH_BALANCE]({
                                'month_income': response.data.chart_financial_bar.totalIncome,
                                'month_cost': response.data.chart_financial_bar.totalCost,
                                'month_profit': response.data.chart_financial_bar.totalProfit
                            });
                            this[types.SET_CHART_DATA](response.data);
                        }
                    },
                    err => {

                        reject(err);
                    }
                );
            },
            getCustomerChartData(customerIdsData){
                let data = {
                    _token: this.csrf,
                    startDate: this.start_date,
                    endDate: this.end_date,
                    saleGroupIds: this.sale_group_ids,
                    userIds: this.user_ids,
                    agencyIdArrays: customerIdsData.type === 'agency' ? customerIdsData.id  : [],
                    clientIdArrays: customerIdsData.type === 'client' ? customerIdsData.id  : [],
                    mediaCompaniesIdArrays: [],
                    mediasNameArrays: [],
                };
                bus.$emit('customerEmptyPie');
                bus.$emit('openCustomerModal');
                this[types.CHANGE_LOADING_STATUS](true);

                axios.post(this.ajax_url, data).then(
                    response => {
                        this[types.CHANGE_LOADING_STATUS](false);
                        if (response.data) {
                            /*sales char*/
                            bus.$emit('customerPrecentagePieUpdate',response.data);
                        }
                    },
                    err => {
                        reject(err);
                    }
                );

            }
        },
        created: function () {
            bus.$on('getCustomerChartData', this.getCustomerChartData);
        },
        mounted: function () {
        },
        watch: {
            start_date: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && val !== oldVal) {
                        this.getData();
                    }
                }
            },
            end_date: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && val !== oldVal) {
                        this.getData();
                    }
                }
            },
            user_ids: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && val !== oldVal) {
                        this.getData();
                    }
                }
            },
            sale_group_ids: {
                immediate: true,
                // lazy:true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && val !== oldVal) {
                        this.getData();
                    }
                }
            },
            agency_ids: {
                immediate: true,
                // lazy:true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && val !== oldVal) {
                        this.getData();
                    }
                }
            },
            client_ids: {
                immediate: true,
                // lazy:true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && val !== oldVal) {
                        this.getData();
                    }
                }
            },
            media_companies_ids: {
                immediate: true,
                // lazy:true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && val !== oldVal) {
                        this.getData();
                    }
                }
            },
            medias_names: {
                immediate: true,
                // lazy:true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '') {
                        this.getData();
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>

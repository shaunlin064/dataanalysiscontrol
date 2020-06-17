<template>
</template>

<script>
    import {mapState, mapMutations, mapActions, mapGetters} from 'vuex';

    export default {
        name: "BonusReviewAjaxCenter",
        props: {
            csrf: String,
        },
        data() {
            return {
                ajax_url : '/bonus/review/getAjaxData',
            }
        },
        computed: {
            ...mapState(['loading']),
            ...mapState('dateRange',['start_date', 'end_date']),
            ...mapState('select',['user_ids', 'sale_group_ids', 'agency_ids', 'client_ids', 'media_companies_ids', 'medias_names']),
            ...mapState('chart',['bonus_char_bar_stack']),
        },
        methods: {
            tableClear: function () {
                $('table').map(function (e, v) {
                    $.fn.dataTable.Api(v).draw();
                    $.fn.dataTable.Api(v).clear();
                });
            }, getData() {
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

                this.$store.dispatch('changeLoadingStatus',true);
                this.tableClear();

                axios.post(this.ajax_url, data).then(
                    response => {
                         this.$store.dispatch('changeLoadingStatus',false);
                        // let total = parseInt(0);
                        if (response.data) {
                            /*sales char*/
                            this.$store.dispatch('chart/changeMoneyStatus', {
                                paid: response.data.chart_money_status.paid,
                                unpaid: response.data.chart_money_status.unpaid
                            });

                            this.$store.dispatch('chart/changeMonthBalance', {
                                'month_income': response.data.chart_financial_bar.totalIncome,
                                'month_cost': response.data.chart_financial_bar.totalCost,
                                'month_profit': response.data.chart_financial_bar.totalProfit
                            });
                            this.$store.dispatch('chart/setChartData',response.data);
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

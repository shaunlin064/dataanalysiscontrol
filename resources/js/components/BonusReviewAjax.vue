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
                time : [],
                ajax_url : '/bonus/review/getAjaxData',
            }
        },
        computed: {
            ...mapState(['start_date', 'end_date', 'user_ids', 'sale_group_ids', 'loading', 'agency_ids', 'client_ids', 'media_companies_ids', 'medias_names','bonus_char_bar_stack']),
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
                    startDate: this.$store.state.start_date,
                    endDate: this.$store.state.end_date,
                    saleGroupIds: this.$store.state.sale_group_ids,
                    userIds: this.$store.state.user_ids,
                    agencyIdArrays: this.$store.state.agency_ids,
                    clientIdArrays: this.$store.state.client_ids,
                    mediaCompaniesIdArrays: this.$store.state.media_companies_ids,
                    mediasNameArrays: this.$store.state.medias_names,
                };

                if ((data.saleGroupIds == '' && data.userIds == '') || data._token === undefined) {
                    return false;
                }

                this.$store.state.loading = true;
                this.tableClear();

                axios.post(this.ajax_url, data).then(
                    response => {
                        this.$store.state.loading = false;
                        // let total = parseInt(0);
                        if (response.data) {
                            /*sales char*/
                            this.$store.commit('changeMoneyStatus', {
                                paid: response.data.chart_money_status.paid,
                                unpaid: response.data.chart_money_status.unpaid
                            });

                            this.$store.commit('changeMonthBalancen', {
                                'month_income': response.data.chart_financial_bar.totalIncome,
                                'month_cost': response.data.chart_financial_bar.totalCost,
                                'month_profit': response.data.chart_financial_bar.totalProfit
                            });

                            this.$store.state.last_record_month_income  = response.data.chart_financial_bar_last_record.totalIncome;
                            this.$store.state.last_record_month_cost  = response.data.chart_financial_bar_last_record.totalCost;
                            this.$store.state.last_record_month_profit = response.data.chart_financial_bar_last_record.totalProfit;
                            this.$store.state.last_record_month_label = response.data.chart_financial_bar_last_record.labels;

                            this.$store.state.chart_bar_max_y =  response.data.chart_bar_max_y;
                            /*sales list*/
                            this.$store.state.group_progress_list = response.data.group_progress_list;
                            this.$store.state.group_progress_list_total = response.data.group_progress_list_total;
                            this.$store.state.progress_list = response.data.progress_list;
                            this.$store.state.progress_list_total = response.data.progress_list_total;
                            /*customer char*/
                            this.$store.state.agency_profit  = response.data.customer_precentage_profit['agency_profit'];
                            this.$store.state.client_profit  = response.data.customer_precentage_profit['client_profit'];
                            this.$store.state.month_label = response.data.customer_precentage_profit['date'];
                            this.$store.state.sale_channel_profitData = response.data.sale_channel_profit_data;

                            /*customer table list*/
                            this.$store.state.customer_groups_profit_data = response.data.customer_groups_profit_data;
                            this.$store.state.customer_profit_data = response.data.customer_profit_data;
                            this.$store.state.medias_profit_data = response.data.medias_profit_data;
                            this.$store.state.media_companies_profit_data = response.data.media_companies_profit_data;

                            /*bonus_list*/
                            this.$store.state.bonus_list = response.data.bonus_list;

                            /*bonus_char_bar_stack*/
                            this.$store.state.bonus_char_bar_stack = response.data.bonus_char_bar_stack;

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
                immediate: true,    // 这句重要
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && val !== oldVal) {
                        this.getData();
                    }
                }
            },
            end_date: {
                immediate: true,    // 这句重要
                // lazy:true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && val !== oldVal) {
                        this.getData();
                    }
                }
            },
            user_ids: {
                immediate: true,    // 这句重要
                // lazy:true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && val !== oldVal) {
                        this.getData();
                    }
                }
            },
            sale_group_ids: {
                immediate: true,// 这句重要
                // lazy:true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && val !== oldVal) {
                        this.getData();
                    }
                }
            },
            agency_ids: {
                immediate: true,// 这句重要
                // lazy:true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && val !== oldVal) {
                        this.getData();
                    }
                }
            },
            client_ids: {
                immediate: true,// 这句重要
                // lazy:true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && val !== oldVal) {
                        this.getData();
                    }
                }
            },
            media_companies_ids: {
                immediate: true,// 这句重要
                // lazy:true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && val !== oldVal) {
                        this.getData();
                    }
                }
            },
            medias_names: {
                immediate: true,// 这句重要
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

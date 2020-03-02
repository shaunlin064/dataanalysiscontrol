<template>

</template>

<script>
    import {mapState, mapMutations, mapActions, mapGetters} from 'vuex';
    
    export default {
        name: "exchangeAjax",
        props: {
            csrf: String,
        },
        data() {
            return {
                ajax_url : '/financial/exchangeRateSetting/getAjaxData',
            }
        },
        computed: {
                ...mapState(['chart_exchange_list','chart_exchange_line','currency','change_date']),
        },
        methods: {
            tableClear: function () {
                $('table').map(function (e, v) {
                    $.fn.dataTable.Api(v).clear();
                    $.fn.dataTable.Api(v).draw();
                });
                
            },
            getData() {
                let data = {
                    _token: this.csrf,
                    year_month: this.$store.state.change_date.replace("/", "") ,
                    currency: this.$store.state.currency,
                };

                if ((data.change_date == '' && data.currency == '') || data._token === undefined) {
                    return false;
                }
                this.$store.state.loading = true;
                this.tableClear();
                
                axios.post(this.ajax_url, data).then(
                    response => {
                        this.$store.state.loading = false;
                        // let total = parseInt(0);
                        if (response.data) {
                            this.$store.state.chart_exchange_line = response.data.exchangeChartData;
                            this.$store.state.exchange_rates_list = response.data.exchangeTableData;
                        }
                        /*業績計算使用當月最後一天即期賣出匯率*/
                        let lastPeriodSell = response.data.exchangeTableData.slice(-1)[0][4];
                        $('#exchange_last').html(lastPeriodSell);
                    },
                    err => {
                        reject(err);
                    }
                );
            },
        },
        beforeMount: function () {
        },
        watch: {
            change_date: {
                immediate: true,    // 这句重要
                    handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '') {
                        this.getData();
                    }
                }
            },
            currency: {
                immediate: true,    // 这句重要
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '') {
                        this.getData();
                    }
                }
            },
        }
    }
</script>

<style scoped>

</style>

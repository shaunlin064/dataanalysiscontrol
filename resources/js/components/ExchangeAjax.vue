<template>

</template>

<script>
    import {mapState, mapMutations, mapActions, mapGetters} from 'vuex';
    import * as types from '../store/types'

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
            ...mapState(['loading']),
            ...mapState('exchangeRate',['currency']),
            ...mapState('datePick',['change_date']),
        },
        methods: {
            ...mapActions([types.CHANGE_LOADING_STATUS]),
            ...mapActions('exchangeRate',[types.CHANGE_CHART_EXCHANGE_LINE,types.CHANGE_EXCHANGE_RATES_LIST,types.CHANGE_CURRENCY]),
            tableClear: function () {
                $('table').map(function (e, v) {
                    $.fn.dataTable.Api(v).clear();
                    $.fn.dataTable.Api(v).draw();
                });
            },
            getData() {
                let data = {
                    _token: this.csrf,
                    year_month: this.change_date.replace("/", "") ,
                    currency: this.currency,
                };
                if ((data.change_date == '' && data.currency == '') || data._token === undefined) {
                    return false;
                }
                this[types.CHANGE_LOADING_STATUS](true);
                this.tableClear();

                axios.post(this.ajax_url, data).then(
                    response => {
                        this[types.CHANGE_LOADING_STATUS](false);
                        if (response.data) {
                            this[types.CHANGE_CHART_EXCHANGE_LINE](response.data.exchangeChartData);
                            this[types.CHANGE_EXCHANGE_RATES_LIST](response.data.exchangeTableData);
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
                immediate: true,
                    handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '') {
                        this.getData();
                    }
                }
            },
            currency: {
                immediate: true,
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

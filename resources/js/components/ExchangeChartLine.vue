<template>
    <div class="box box-info" id="canvas-holder" :style="{
            'height': `${height ? height : 'auto'}px`
     }">
        <canvas :id=table_id></canvas>
    </div>
</template>

<script>
    import {mapState, mapMutations, mapActions, mapGetters} from 'vuex';

    export default {
        name: "ExchangeChartLine",
        props: {
            table_id: String,
            title: String,
            chart_data: Array,
            item_labels:Array,
            labels: Array,
            ajax_url: String,
            csrf: String,
            height:Number
        },
        data() {
            return {
                default_color: {
                    red: 'rgba(255, 99, 132,0.5)',
                    orange: 'rgba(255, 159, 64,0.5)',
                    yellow: 'rgba(255, 205, 86,0.5)',
                    green: 'rgba(75, 192, 192,0.5)',
                    blue: 'rgba(54, 162, 235,0.5)',
                    purple: 'rgba(153, 102, 255,0.5)',
                    grey: 'rgba(201, 203, 207,0.5)'
                },
                color: [
                    'rgba(255, 99, 132,0.5)',
                    'rgba(54, 162, 235,0.5)',
                    'rgba(255, 99, 132,0.5)',
                    'rgba(54, 162, 235,0.5)',
                ],
                label: this.item_labels,
                config: {
                    type: 'line',
                    data: {
                        'datasets': this.chart_data,
                        'labels': this.labels,
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: this.height ? false : true,
                        title: {
                            display: true,
                            text: this.title
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: false,
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        },
                        scales: {
                            xAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: ['Month']
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Value'
                                }
                            }]
                        }
                    }
                },
                chart_obj: {},
                chart_labels: [],
            }
        },
        computed: {
                ...mapState('exchangeRate',['chart_exchange_line']),
        },
        methods: {
            update(vue_this) {
                vue_this.chart_obj.data.datasets.map(function (dataset, key) {

                    if (key == 0) {
                        switch (vue_this.table_id) {
                            case 'chart_exchange_line_money':
                                let moneydata = [
                                    vue_this.chart_exchange_line.moneyBuy,
                                    vue_this.chart_exchange_line.moneySell
                                ];
                                vue_this.chart_obj.data.datasets.map(function (dataset, key) {
                                    dataset.data = moneydata[key];
                                });
                                break;
                            case 'chart_exchange_line_period':
                                let perioddata = [
                                    vue_this.chart_exchange_line.periodBuy,
                                    vue_this.chart_exchange_line.periodSell
                                ];
                                vue_this.chart_obj.data.datasets.map(function (dataset, key) {
                                    dataset.data = perioddata[key];
                                });
                                break;
                        }

                        vue_this.chart_obj.data.labels = vue_this.chart_exchange_line.labels;
                    }

                });
                vue_this.chart_obj.update({
                    duration: 700,
                    easing: 'linear'});
            },
        },
        beforeMount: function () {
        },
        created: function () {
            let vue_this = this;
            this.config.data.datasets.map(function (v, k, i) {
                    v.label = vue_this.label[k];
                    v.backgroundColor = vue_this.color[k];
                    v.borderColor = vue_this.color[k];
                    v.fill= false;
            });
        },
        mounted: function () {
            var ctx = document.getElementById(this.table_id).getContext('2d');
            this.chart_obj = new Chart(ctx, this.config);
        },
        watch: {
            chart_exchange_line:{
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '') {
                        this.update(this);
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>

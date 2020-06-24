<template>
    <div class="box box-info" :id="'canvas-holder-'+table_id" :style="{
            'height': `${height ? height : 'auto'}px`
     }">
        <canvas :id=table_id></canvas>
    </div>
</template>

<script>
    import {mapActions, mapState , mapGetters} from 'vuex';
    import * as types from "../store/types";
    export default {
        name: "chart_stack",
        props: {
            table_id: String,
            title: String,
            height: Number,
            row: Array,
        },
        data() {
            return {
                color: [
                    'rgba(177,12,71,0.5)',
                    'rgba(111,222,170,0.5)',
                    'rgba(83,208,185,0.5)',
                    'rgba(75, 192, 192,0.5)',
                    'rgba(251,171,126,0.5)',
                    'rgba(255, 177, 145,0.5)',
                    'rgba(255, 183, 167,0.5)',
                    'rgba(142,197,252,0.5)',
                    'rgba(143, 171, 235,0.5)',
                    'rgba(149, 144, 213,0.5)',
                    'rgba(255, 99, 132,0.5)',
                    'rgba(216,62,100,0.5)',
                    'rgba(90,95,116,0.5)',
                    'rgba(201, 203, 207,0.5)',
                    'rgba(229,210,195,0.5)',
                    'rgba(204,176,172,0.5)',
                    'rgba(173,145,9,0.5)',
                    'rgba(140,122,159,0.5)',
                    'rgba(179,138,174,0.5)',
                    'rgba(218,155,183,0.5)',
                    'rgba(251,162,174,0.5)',
                ],
                group_id_color: {
                    1: 'rgba(142,197,252,0.5)',
                    2: 'rgba(251,171,126,0.5)',
                    3: 'rgba(255, 99, 132,0.5)',
                    4: 'rgba(255, 159, 64,0.5)',
                    5: 'rgba(201, 203, 207,0.5)'
                },
                default_color: {
                    red: 'rgba(255, 99, 132,0.5)',
                    orange: 'rgba(255, 159, 64,0.5)',
                    yellow: 'rgba(255, 205, 86,0.5)',
                    green: 'rgba(75, 192, 192,0.5)',
                    blue: 'rgba(54, 162, 235,0.5)',
                    purple: 'rgba(153, 102, 255,0.5)',
                    grey: 'rgba(201, 203, 207,0.5)'
                },
                config: {
                    type: 'bar',
                    data: {
                        'datasets': [],
                        'labels': [],
                    },
                    options: {
                        maintainAspectRatio: this.height ? false : true,
                        title: {
                            display: true,
                            text: this.title
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: false
                        },
                        responsive: true,
                        scales: {
                            xAxes:  [{
                                stacked: true,
                                scaleLabel : {
                                    display : true,
                                    labelString : '',
                                    padding: 2,
                                }
                            }],
                            yAxes: [{
                                stacked: true,
                            }]
                        },
                        plugins: {
                            datalabels: {
                                formatter: (value, ctx) => {
                                    let sum = 0;
                                    let index = ctx.datasetIndex;
                                    let dataArr = ctx.chart.data.datasets[index].data;

                                    dataArr.map(data => {
                                        if(data < 0){
                                            data = 0;
                                        }
                                        sum += data;
                                    });

                                        let strNumber = value;
                                        if(value/1000000000 > 1){
                                            strNumber = (value / 1000000000).toFixed(1) + 'G'
                                        }
                                        else if(value/1000000 > 1){
                                            strNumber = (value / 1000000).toFixed(1) + 'M'
                                        }else if(value/1000 > 1){
                                            strNumber = Math.round(value / 1000) + 'k'
                                        }else{
                                            strNumber = Math.round(value * 1000) / 1000;
                                        }
                                        // strNumber = currencyFilters(parseInt(Math.round(value * 1000) / 1000));
                                        return strNumber;
                                },
                                color: '#0c0b0b',
                            }
                        },
                    }
                },
            }
        },
        computed: {
            ...mapState('chart',['bonus_char_bar_stack']),
            ...mapState('dateRange',['start_date','end_date'])
        },
        created: function () {
        },
        mounted: function () {
            var ctx = document.getElementById(this.table_id).getContext('2d');
            this.chart_obj = new Chart(ctx, this.config);
        },
        methods: {
            update() {
                /*資料整理*/
                let barChartData = this.trimData();
                this.chart_obj.data = barChartData;

                this.chart_obj.options.scales.xAxes[0].scaleLabel.labelString = `${this.start_date.substr(0, 7)}  至  ${this.end_date.substr(0, 7)}`;

                this.chart_obj.options.scales.xAxes[0].scaleLabel.padding= 2;
                this.chart_obj.update({
                    duration: 0,
                    easing: 'linear'
                });

            },
            trimData() {
                /*full month*/
                let barChartData = {};
                let trimDatas = [];
                let originalData = this.bonus_char_bar_stack;
                let stackLabel = [];
                let columnLabel = [];
                var vue = this;
                let i = 0;
                var items = [];
                var items2 = [];
                var items3 = [];
                Object.keys(originalData).forEach(userName => {
                    columnLabel.push(userName);
                    items.push(originalData[userName]['income']);
                    items2.push(originalData[userName]['cost']);
                    items3.push(originalData[userName]['profit']);
                }, trimDatas, stackLabel, columnLabel, i);

                trimDatas.push({
                    label: `收入`,
                    data: items,
                    stack: '0',
                    backgroundColor: vue.color[10]
                });

                trimDatas.push({
                    label: `成本`,
                    data: items2,
                    stack: '1',
                    backgroundColor: vue.color[7]
                });
                trimDatas.push({
                    label: `毛利`,
                    data: items3,
                    stack: '2',
                    backgroundColor: vue.color[3]
                });
                barChartData.labels = columnLabel;
                barChartData.datasets = trimDatas;
                return barChartData;
            }
        },
        beforeMount: function () {

        },
        watch: {
            bonus_char_bar_stack: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined) {
                        this.update();
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>

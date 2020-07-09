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
            ...mapState('chart',['bonus_char_bar_stack','color','group_id_color','default_color']),
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

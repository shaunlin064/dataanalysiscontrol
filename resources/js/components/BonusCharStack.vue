<template>
    <div class="box box-info" :id="'canvas-holder-'+table_id" :style="{
            'height': `${height ? height : 'auto'}px`
     }">
        <canvas :id=table_id></canvas>
    </div>
</template>

<script>
    import {mapState} from 'vuex';

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
                            xAxes: [{
                                stacked: true,
                            }],
                            yAxes: [{
                                stacked: true,
                            }]
                        },
                    }
                }
            }
        },
        computed: {
            ...mapState(['bonus_char_bar_stack']),
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
                this.chart_obj.update({
                    duration: 0,
                    easing: 'linear'
                });

            },
            trimData() {
                /*stack*/
                // let barChartData = {};
                // let trimDatas = [];
                // let originalData = this.bonus_char_bar_stack;
                // let stackLabel = [];
                // let columnLabel = [];
                // var vue = this;
                // let i = 0;
                //
                // Object.keys(originalData).forEach(date=>{
                //     // trimDatas[key] = [];
                //     let items = [];
                //     let items2 = [];
                //     let items3 = [];
                //     let datas = originalData[date];
                //
                //     stackLabel.push(date);
                //     Object.keys(datas).forEach(userName =>{
                //         if(i == 0){
                //             columnLabel.push(userName);
                //         }
                //         items.push(datas[userName]['income']);
                //         items2.push(datas[userName]['cost']);
                //         items3.push(datas[userName]['profit']);
                //     },columnLabel,i);
                //
                //     let colorNumber = parseInt(date.substr(5,6))%12;
                //     trimDatas.push({
                //         label:`${date}-收入`,
                //         data:items,
                //         stack:'0',
                //         backgroundColor:vue.color[colorNumber]
                //     });
                //
                //     trimDatas.push({
                //         label:`${date}-成本`,
                //         data:items2,
                //         stack:'1',
                //         backgroundColor:vue.color[colorNumber]
                //     });
                //     trimDatas.push({
                //         label:`${date}-毛利`,
                //         data:items3,
                //         stack:'2',
                //         backgroundColor:vue.color[colorNumber]
                //     });
                //
                //     i++;
                //
                // },trimDatas,stackLabel,columnLabel,i);
                // barChartData.labels = columnLabel;
                // barChartData.datasets = trimDatas;
                //
                // return barChartData;
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

<template>
    <div class="box box-info" id="canvas-holder">
        <canvas :id=table_id></canvas>
    </div>
</template>

<script>
    import {mapActions, mapState} from 'vuex';

    export default {
        name: "MediaPrecentagePie",
        props: {
            table_id: String,
            title: String,
            labels: Array,
        },
        computed: {...mapState(['medias_profit_data', 'sale_channel_profitData'])},
        data: function () {
            return {
                evTimeStamp : 0,
                drill_down_data:{

                },
                drill_position: '',
                chart_data : [],
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
                    // 'rgba(255, 99, 132,0.5)',
                    // 'rgba(54, 162, 235,0.5)',
                    'rgba(255, 205, 86,0.5)',
                    'rgba(75, 192, 192,0.5)',
                    'rgba(153, 102, 255,0.5)',
                ],
                channel_color: {
                    'AP': 'rgba(255, 205, 86,0.5)',
                    'BR': 'rgba(75, 192, 192,0.5)',
                    'EC': 'rgba(153, 102, 255,0.5)',
                },
                config: {
                    type: 'pie',
                    data: {
                        'datasets': [
                            {"data": [0, 0, 0, 0, 0]},
                            {
                                "data": [0, 0, 0, 0, 0]
                            }],
                        'labels': this.labels,
                    },
                    options: {
                        responsive: true,
                        title: {
                            display: true,
                            text: this.title
                        },
                        plugins: {
                            datalabels: {
                                labels: {
                                    // index: {
                                    //     align: 'end',
                                    //     anchor: 'end',
                                    //     color: function(ctx) {
                                    //         return ctx.dataset.backgroundColor;
                                    //     },
                                    //     font: {size: 18},
                                    //     formatter: function(value, ctx) {
                                    //         if(value !== 0){
                                    //             return ctx.active
                                    //                 ? 'index'
                                    //                 : '#' + (ctx.dataIndex + 1);
                                    //         }else{
                                    //             return null;
                                    //         }
                                    //     },
                                    //     offset: 8,
                                    //     opacity: function(ctx) {
                                    //         return ctx.active ? 1 : 0.5;
                                    //     }
                                    // },
                                    name: {
                                        align: 'top',
                                        font: {size: 16},
                                        formatter: function (value, ctx) {
                                            if (value !== 0) {
                                                return ctx.active
                                                    ? ctx.chart.data.labels[ctx.dataIndex]
                                                    : ctx.chart.data.labels[ctx.dataIndex];
                                            } else {
                                                return null;
                                            }
                                        }
                                    },
                                    value: {
                                        align: 'bottom',
                                        backgroundColor: function (ctx) {
                                            // var value = ctx.dataset.data[ctx.dataIndex];
                                            return 'white';
                                        },
                                        borderColor: 'white',
                                        borderWidth: 2,
                                        borderRadius: 4,
                                        color: function (ctx) {
                                            var value = ctx.dataset.data[ctx.dataIndex];
                                            return value > 0
                                                ? 'black'
                                                : 'red';
                                        },
                                        formatter: function (value, ctx) {
                                            if (value !== 0) {
                                                let sum = 0;
                                                let index = ctx.datasetIndex;
                                                let dataArr = ctx.chart.data.datasets[index].data;

                                                dataArr.map(data => {
                                                    if (data < 0) {
                                                        data = 0;
                                                    }
                                                    sum += data;
                                                });

                                                let percentage = (value * 100 / sum).toFixed(1);
                                                return ctx.active
                                                    ? Math.round(value * 1000) / 1000
                                                    : percentage + "%";

                                            } else {
                                                return null;
                                            }


                                        },
                                        padding: 4,
                                    }
                                },
                                // formatter: (value, ctx) => {
                                //     let sum = 0;
                                //     let index = ctx.datasetIndex;
                                //     let dataArr = ctx.chart.data.datasets[index].data;
                                //
                                //     dataArr.map(data => {
                                //         if(data < 0){
                                //             data = 0;
                                //         }
                                //         sum += data;
                                //     });
                                //
                                //     let percentage = (value*100 / sum).toFixed(1);
                                //     if(percentage != '0.0'){
                                //         return percentage+"%";
                                //     }
                                //     return null;
                                //
                                // },
                                // color: '#0c0b0b',
                            }
                        }
                    }
                },
                chart_obj: {},
                chart_labels: [],
            }
        },
        created: function () {
            let vue = this;
            this.config.data.datasets.map(function (v, k, i) {
                v.backgroundColor = vue.color;
            });
        },
        mounted: function () {
            var ctx = document.getElementById(this.table_id).getContext('2d');
            this.chart_obj = new Chart(ctx, this.config);
            var canvas = document.getElementById(this.table_id);
            let vue = this;
            canvas.onclick = function (evt) {
                var activePoints = vue.chart_obj.getElementsAtEvent(evt);
                if (activePoints[0]) {
                    let chartData = activePoints[0]['_chart'].config.data;
                    let idx = activePoints[0]['_index'];
                    let value = chartData.datasets[0].data[idx];
                    if (idx <= 2) {
                        let value = chartData.datasets[1].data[idx];
                    }
                    let label = chartData.labels[idx];
                    vue.drilldown(label);
                }
            };
        },
        methods: {
            drilldown(label) {
                let vue = this;

                if(vue.drill_down_data[label] === undefined){
                    return false;
                }
                vue.chart_obj.data.labels = [];
                vue.chart_obj.data.datasets[0].backgroundColor = [];
                vue.chart_obj.data.datasets[1].backgroundColor = [];
                let data = [];
                let mediaData = [];
                let channelData = [];

                if(this.drill_position === label){
                    Object.keys(vue.sale_channel_profitData).forEach(key => {
                        let label = key;
                        let color = vue.channel_color[key];
                        let money = vue.sale_channel_profitData[key];
                        channelData.push(money);
                        vue.chart_obj.data.datasets[0].backgroundColor.push(color);
                        vue.chart_obj.data.datasets[1].backgroundColor.push(color);
                        mediaData.push(0);
                        vue.chart_obj.data.labels.push(label);
                    });
                    vue.chart_data = vue.chart_data.sort(function (a, b) {
                        return a.sales_channel < b.sales_channel ? 1 : -1;
                    });
                    vue.chart_data.map((v, key) => {
                        let label = v.name;
                        let color = vue.channel_color[v.sales_channel];
                        let money = parseInt(v.profit);
                        channelData.push(0);
                        vue.chart_obj.data.datasets[0].backgroundColor.push(color);
                        vue.chart_obj.data.datasets[1].backgroundColor.push(color);
                        mediaData.push(money);
                        vue.chart_obj.data.labels.push(label);
                    });
                    this.drill_position = 'all';
                }else{
                    /*內圈*/
                    let color = vue.channel_color[label];
                    let money = vue.sale_channel_profitData[label];
                    if( label == 'other'){
                        money = vue.drill_down_data[label]['profit'];
                    }
                    channelData.push(money);
                    vue.chart_obj.data.datasets[0].backgroundColor.push(color);
                    vue.chart_obj.data.datasets[1].backgroundColor.push(color);
                    mediaData.push(0);
                    vue.chart_obj.data.labels.push(label);
                    /*外圈*/
                    vue.drill_down_data[label]['child'].map((v, key) => {
                        let label = v.name;
                        let color = vue.channel_color[v.sales_channel];
                        let money = parseInt(v.profit);
                        channelData.push(0);
                        vue.chart_obj.data.datasets[0].backgroundColor.push(color);
                        vue.chart_obj.data.datasets[1].backgroundColor.push(color);
                        mediaData.push(money);
                        vue.chart_obj.data.labels.push(label);
                    });
                    this.drill_position = label;
                }

                data.push(mediaData);
                data.push(channelData);
                this.chart_obj.data.datasets.map(function (dataset, key) {
                    dataset.data = data[key];
                });
                this.chart_obj.update({
                    duration: 700,
                    easing: 'linear'
                });

            },
            sumDataReduce(arr) {
                return arr.reduce((a, b) => a + b);
            },
            update() {
                let vue = this;
                vue.chart_obj.data.labels = [];
                vue.chart_obj.data.datasets[0].backgroundColor = [];
                vue.chart_obj.data.datasets[1].backgroundColor = [];
                vue.drill_down_data = {
                };
                vue.chart_data = [];
                let data = [];
                let mediaData = [];
                let channelData = [];

                /*計算媒體利潤平均值*/
                let media_total_profit = 0;
                let trimData = Object.assign([],this.medias_profit_data)
                trimData.map((v) => {
                    let money = parseInt(v.profit);
                    media_total_profit += money;
                });
                let media_length = trimData.length;
                let profit_average = Math.round(media_total_profit/media_length);
                /*取前10 且 profit 大於平均*/
                let topNumber = 5;

                /*依利潤排序*/
                let tmp_data = trimData;
                tmp_data = tmp_data.sort(function (a, b) {
                    return a.profit < b.profit ? 1 : -1;
                });
                /*分出大於平均值資料 與 其他*/
                let other_data = [];
                tmp_data.map((v,key)=>{
                    /*取前10 且 profit 大於平均*/
                    if(v.profit >=  profit_average && key < topNumber){
                        vue.chart_data.push(v);
                    }else{
                        other_data.push(v);
                    }
                    if(vue.drill_down_data[v.sales_channel] === undefined){
                        vue.drill_down_data[v.sales_channel] = {
                            'name' : v.sales_channel,
                            'cost' : 0,
                            'income' : 0,
                            'profit' : 0,
                            'profit_percenter' : 0,
                            'sales_channel' : v.sales_channel,
                            'child' : []
                        };
                    }
                    vue.drill_down_data[v.sales_channel]['cost'] += v.cost;
                    vue.drill_down_data[v.sales_channel]['income'] += v.income;
                    vue.drill_down_data[v.sales_channel]['profit'] += v.profit;
                    vue.drill_down_data[v.sales_channel]['child'].push(v);
                });

                ['AP','BR','EC'].map(k=>{
                    let tmpChild = [];
                    let tmp_other = {
                        'name' : '其他',
                        'cost' : 0,
                        'income' : 0,
                        'profit' : 0,
                        'profit_percenter' : 0,
                        'sales_channel' : k,
                        'child' : []
                    };
                    let average = vue.drill_down_data[k]['profit'] / vue.drill_down_data[k]['child'].length;

                    vue.drill_down_data[k]['child'].map((v,key)=>{
                        if(key <= 4 && v.profit >= average && v.profit > 0 && v.profit > 10000){
                            tmpChild.push(v)
                        }else{
                            tmp_other['cost'] += v.cost;
                            tmp_other['income'] += v.income;
                            tmp_other['profit'] += v.profit;
                            tmp_other.child.push(v);
                        }
                    });
                    tmpChild.push(tmp_other);
                    vue.drill_down_data[k]['child'] = tmpChild;
                })


                /*trim data 整合*/
                vue.drill_down_data['other'] = {
                    'name' : 'other',
                    'cost' : 0,
                    'income' : 0,
                    'profit' : 0,
                    'profit_percenter' : 0,
                    'sales_channel' : 'other',
                    'child' : []
                };

                vue.drill_down_data['other']['cost'] = sumDataMapReduce(other_data,'cost');
                vue.drill_down_data['other']['income'] =  sumDataMapReduce(other_data,'income');
                vue.drill_down_data['other']['profit'] = sumDataMapReduce(other_data,'profit');

                let tmp_insite_other = {
                    'name' : '其他',
                    'cost' : 0,
                    'income' : 0,
                    'profit' : 0,
                    'profit_percenter' : 0,
                    'sales_channel' : 'other',
                    'child' : []
                };
                let average = vue.drill_down_data['other']['profit'] / other_data.length;
                other_data.map((v,key)=>{
                    if(key <= 4 && v.profit > average && v.profit > 10000){
                        vue.drill_down_data['other']['child'].push(v)
                    }else{
                        tmp_insite_other['cost'] += v.cost;
                        tmp_insite_other['income'] += v.income;
                        tmp_insite_other['profit'] += v.profit;
                        tmp_insite_other.child.push(v);
                    }
                });
                vue.drill_down_data['other']['child'].push(tmp_insite_other);



                vue.chart_data.push(vue.drill_down_data['other']);
                Object.keys(vue.sale_channel_profitData).forEach(key => {
                    let label = key;
                    let color = vue.channel_color[key];
                    let money = vue.sale_channel_profitData[key];
                    channelData.push(money);
                    vue.chart_obj.data.datasets[0].backgroundColor.push(color);
                    vue.chart_obj.data.datasets[1].backgroundColor.push(color);
                    mediaData.push(0);
                    vue.chart_obj.data.labels.push(label);
                });
                vue.chart_data = vue.chart_data.sort(function (a, b) {
                    return a.sales_channel > b.sales_channel ? 1 : -1;
                });


                vue.chart_data.map((v, key) => {
                    let label = v.name;
                    let color = vue.channel_color[v.sales_channel];
                    let money = parseInt(v.profit);
                    channelData.push(0);
                    vue.chart_obj.data.datasets[0].backgroundColor.push(color);
                    vue.chart_obj.data.datasets[1].backgroundColor.push(color);
                    mediaData.push(money);
                    vue.chart_obj.data.labels.push(label);
                });


                data.push(mediaData);
                data.push(channelData);
                vue.chart_obj.data.datasets.map(function (dataset, key) {
                    dataset.data = data[key];
                });
                vue.chart_obj.update({
                    duration: 700,
                    easing: 'linear'
                });
            },
            ...mapActions({
                saveName: 'saveName'
            }),
        },
        watch: {
            medias_profit_data: {
                deep: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val != oldVal) {

                        let now = +new Date();
                        if (now - this.evTimeStamp < 2000) {
                           return;
                        }
                        console.log('1');
                        this.evTimeStamp = now;
                        this.update();
                    }
                }
            },
            // client_profit: {
            //     immediate: true,    // 这句重要
            //     handler(val, oldVal) {
            //         if (oldVal !== undefined ) {
            //             this.update(this);
            //         }
            //     }
            // },
        }
    }
</script>

<style scoped>

</style>

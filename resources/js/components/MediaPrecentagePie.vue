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
                topNumber: 5,
                topProfitLimit: 10000,
                evTimeStamp: 0,
                chart_drill_down_data: {},
                drill_position: '',
                chart_outside_data: [],
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
                                                let strNumber = value;
                                                // if(value/1000000000 > 1){
                                                //     strNumber = (value / 1000000000).toFixed(1) + 'B'
                                                // }
                                                // else if(value/1000000 > 1){
                                                //     strNumber = (value / 1000000).toFixed(1) + 'M'
                                                // }else if(value/1000 > 1){
                                                //     strNumber = Math.round(value / 1000) + 'K'
                                                // }else{
                                                //     strNumber = Math.round(value * 1000) / 1000;
                                                // }
                                                strNumber = currencyFilters(parseInt(Math.round(value * 1000) / 1000));
                                                return ctx.active
                                                    ? strNumber
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
            getMediaCopyData() {
                return Object.assign([], this.medias_profit_data);
            },
            getDataDefault(name, sales_channel) {
                return {
                    'name': name,
                    'cost': 0,
                    'income': 0,
                    'profit': 0,
                    'profit_percenter': 0,
                    'sales_channel': sales_channel,
                    'child': []
                }
            },
            drilldown(label) {
                let vue = this;

                if (vue.chart_drill_down_data[label] === undefined) {
                    return false;
                }
                vue.chart_obj.data.labels = [];
                vue.chart_obj.data.datasets[0].backgroundColor = [];
                vue.chart_obj.data.datasets[1].backgroundColor = [];
                let data = [];
                let mediaData = [];
                let inSiteData = [];

                if (this.drill_position === label) {

                    Object.keys(vue.sale_channel_profitData).forEach(key => {
                        let label = key;
                        let color = vue.channel_color[key];
                        let money = vue.sale_channel_profitData[key];
                        inSiteData.push(money);
                        vue.chart_obj.data.datasets[0].backgroundColor.push(color);
                        vue.chart_obj.data.datasets[1].backgroundColor.push(color);
                        mediaData.push(0);
                        vue.chart_obj.data.labels.push(label);
                    });
                    vue.chart_outside_data = vue.chart_outside_data.sort(function (a, b) {
                        return a.sales_channel < b.sales_channel ? 1 : -1;
                    });
                    vue.chart_outside_data.map((v, key) => {
                        let label = v.name;
                        let color = vue.channel_color[v.sales_channel];
                        let money = parseInt(v.profit);
                        inSiteData.push(0);
                        vue.chart_obj.data.datasets[0].backgroundColor.push(color);
                        vue.chart_obj.data.datasets[1].backgroundColor.push(color);
                        mediaData.push(money);
                        vue.chart_obj.data.labels.push(label);
                    });
                    this.drill_position = 'all';
                } else {
                    /*內圈*/
                    let color = vue.channel_color[label];
                    let money = vue.sale_channel_profitData[label];
                    if (label == 'other') {
                        money = vue.chart_drill_down_data[label]['profit'];
                    }
                    inSiteData.push(money);
                    vue.chart_obj.data.datasets[0].backgroundColor.push(color);
                    vue.chart_obj.data.datasets[1].backgroundColor.push(color);
                    mediaData.push(0);
                    vue.chart_obj.data.labels.push(label);
                    /*外圈*/
                    vue.chart_drill_down_data[label]['child'].map((v, key) => {
                        let label = v.name;
                        let color = vue.channel_color[v.sales_channel];
                        let money = parseInt(v.profit);
                        inSiteData.push(0);
                        vue.chart_obj.data.datasets[0].backgroundColor.push(color);
                        vue.chart_obj.data.datasets[1].backgroundColor.push(color);
                        mediaData.push(money);
                        vue.chart_obj.data.labels.push(label);
                    });
                    this.drill_position = label;
                }

                data.push(mediaData);
                data.push(inSiteData);
                this.chart_obj.data.datasets.map(function (dataset, key) {
                    dataset.data = data[key];
                });
                this.chart_obj.update({
                    duration: 700,
                    easing: 'linear'
                });

            },
            getOtherData(data) {
                let vue = this;
                let profit = sumDataMapReduce(data, 'profit');
                let profit_average = profit / data.length;
                let trimData = [];
                let tmp_other = vue.getDataDefault('other', 'other');
                data.map((v, key) => {
                    let inTop = false;
                    inTop = v.profit >= profit_average && v.profit > vue.topProfitLimit && v.profit > 0 && key < vue.topNumber;
                    if (inTop) {
                        trimData.push(v);
                    } else {
                        tmp_other['child'].push(v);
                    }
                });
                tmp_other = vue.getTrimMediaChildData(tmp_other['child'], 'other', '其他');
                vue.chart_drill_down_data['other'] = tmp_other;
                trimData.push(tmp_other)

                return trimData;
            },
            getTrimMediaChildData(data, name, sales_channel) {
                let vue = this;
                let trimData = vue.getDataDefault(name, sales_channel);
                let tmp_other = vue.getDataDefault('其他', sales_channel);
                trimData.profit = sumDataMapReduce(data, 'profit');
                trimData.cost = sumDataMapReduce(data, 'cost');
                trimData.income = sumDataMapReduce(data, 'income');
                let profit_average = trimData.profit / data.length;

                data.map((v, key) => {
                    let inTop = false;
                    inTop = v.profit >= profit_average && v.profit > vue.topProfitLimit && v.profit > 0 && key < vue.topNumber;
                    if (inTop) {
                        trimData['child'].push(v)
                    } else {
                        tmp_other['cost'] += v.cost;
                        tmp_other['income'] += v.income;
                        tmp_other['profit'] += v.profit;
                        tmp_other['child'].push(v);
                    }
                });
                trimData['child'].push(tmp_other)

                return trimData;
            },
            setRenderChart(data, type) {
                let vue = this;
                let [inSiteData, outSiteData] = [[], []]
                Object.keys(data).forEach(key => {
                    let label = '';
                    let color = '';
                    let money = '';
                    if (type === 'in') {
                        label = key;
                        color = vue.channel_color[key];
                        money = vue.sale_channel_profitData[key];
                        inSiteData.push(money);
                        outSiteData.push(0);
                    } else {
                        label = data[key].name;
                        color = vue.channel_color[data[key].sales_channel];
                        money = parseInt(data[key].profit);
                        inSiteData.push(0);
                        outSiteData.push(money);
                    }
                    vue.chart_obj.data.datasets[0].backgroundColor.push(color);
                    vue.chart_obj.data.datasets[1].backgroundColor.push(color);

                    vue.chart_obj.data.labels.push(label);
                });
                vue.chart_obj.data.datasets[0].data= vue.chart_obj.data.datasets[0].data.concat(outSiteData);
                vue.chart_obj.data.datasets[1].data = vue.chart_obj.data.datasets[1].data.concat(inSiteData);

            },
            chartRestDefault(){
                this.chart_obj.data.labels = [];
                this.chart_obj.data.datasets[0].data = [];
                this.chart_obj.data.datasets[1].data = [];
                this.chart_obj.data.datasets[0].backgroundColor = [];
                this.chart_obj.data.datasets[1].backgroundColor = [];
            },
            update() {
                let vue = this;
                let media_total_profit = 0;
                let profit_average = 0;
                let sortSalesChannelData = {};

                vue.chart_drill_down_data = {};
                vue.chart_outside_data = [];
                vue.chartRestDefault();

                /*計算媒體利潤平均值*/
                let trimData = vue.getMediaCopyData();

                if (trimData.length > 0) {
                    /*依利潤排序*/
                    trimData = trimData.sort((a, b) => a.profit < b.profit ? 1 : -1);
                    media_total_profit = sumDataMapReduce(trimData, 'profit');
                    profit_average = Math.round(media_total_profit / trimData.length);
                    /*sort data*/
                    trimData.map((v, key) => {
                        sortSalesChannelData[v.sales_channel] = checkUndefined(sortSalesChannelData, v.sales_channel, []);
                        sortSalesChannelData[v.sales_channel].push(v);
                    });
                    /*trim 鉗套下層資料*/
                    Object.keys(sortSalesChannelData).forEach(sales_channel => {
                        vue.chart_drill_down_data[sales_channel] = vue.getTrimMediaChildData(sortSalesChannelData[sales_channel], sales_channel, sales_channel);
                    });
                    /*最上層層資料*/
                    vue.chart_outside_data = vue.getOtherData(trimData).sort((a, b) => a.sales_channel > b.sales_channel ? 1 : -1);

                    /*render chart*/
                    vue.setRenderChart(vue.sale_channel_profitData, 'in');
                    vue.setRenderChart(vue.chart_outside_data, 'out');
                }

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

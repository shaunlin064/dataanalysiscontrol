<template>
    <div class="box box-info" id="canvas-holder">
        <canvas :id=table_id></canvas>
    </div>
</template>

<script>
    import {mapActions, mapState} from 'vuex';

    export default {
        name: "CustomerPrecentagePie",
        props: {
            table_id: String,
        },
        computed: {
            ...mapState('chart', ['agency_profit', 'client_profit', 'sale_channel_profitData','color','group_id_color','default_color'])
        },
        data: function () {
            return {
                config: {
                    type: 'pie',
                    data: {
                        'datasets': [
                            {"data": [0, 0, 0, 0, 0]},
                            {"data": [0, 0, 0, 0, 0]},
                            {"data": [0, 0, 0, 0, 0]}],
                        'labels': [],
                    },
                    options: {
                        responsive: true,
                        title: {
                            display: true,
                            text: ''
                        },
                        scales: {
                            // xAxes:  [{
                            //     display: true,
                            //     stacked: false,
                            //     scaleLabel : {
                            //         display : false,
                            //     }
                            // }],
                            // yAxes: [{
                            //     display: true,
                            //     stacked: false,
                            //     scaleLabel : {
                            //         display : false,
                            //     }
                            // }]
                        },
                        plugins: {
                            datalabels: {
                                labels: {
                                    name: {
                                        align: 'top',
                                        font: {size: 16},
                                        formatter: function (value, ctx) {
                                            if (value !== 0) {

                                                return ctx.active
                                                    ? ctx.dataset.priceName
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

                                                strNumber = parseInt(Math.round(value * 1000) / 1000).toLocaleString('en-US');
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
                            }
                        }
                    }
                },
                chart_obj: {},
                chart_labels: [],
            }
        },
        created: function () {
            let vue_this = this;
            // this.config.data.datasets.map(function (v, k, i) {
            //     v.backgroundColor = vue_this.color;
            // });
            bus.$on('customerPrecentagePieUpdate', this.customerPrecentagePieUpdate);
            bus.$on('customerEmptyPie', this.customerEmptyPie);

        },
        mounted: function () {
            var ctx = document.getElementById(this.table_id).getContext('2d');
            this.chart_obj = new Chart(ctx, this.config);
        },
        methods: {
            customerEmptyPie(){
                this.chart_obj.data.labels = [];
                this.chart_obj.data.datasets[0].data = [];
                this.chart_obj.data.datasets[1].data = [];
                this.chart_obj.data.datasets[2].data = [];
                this.chart_obj.data.datasets[2].priceName = '收入';
                this.chart_obj.data.datasets[1].priceName = '毛利';
                this.chart_obj.data.datasets[0].priceName = '成本';
                this.chart_obj.titleBlock.options.text = 'loading....';
                this.chart_obj.config.data.datasets[0].backgroundColor  = [];
                this.chart_obj.config.data.datasets[1].backgroundColor  = [];
                this.chart_obj.config.data.datasets[2].backgroundColor  = [];
                // this.chart_obj.config.data.datasets[2].backgroundColor = [this.default_color.red,this.default_color.blue];
                this.chart_obj.update();
            },
            customerPrecentagePieUpdate(data) {
                let bonus_char_bar_stack = data.bonus_char_bar_stack;
                let media_companies_profit_data = data.media_companies_profit_data;
                let chart_money_status = data.chart_money_status;
                let color = [this.default_color.red,this.default_color.blue];

                Object.keys(chart_money_status).forEach(key=>{
                    this.chart_obj.data.datasets[2].data.push(parseInt(chart_money_status[key]));
                    this.chart_obj.data.datasets[1].data.push(0);
                    this.chart_obj.data.datasets[0].data.push(0);
                    this.chart_obj.data.labels.push(key === 'unpaid' ? '未收款' : '已收款' );
                });﻿

                Object.keys(bonus_char_bar_stack).forEach(key=>{
                    color.push(this.group_id_color[bonus_char_bar_stack[key]['sale_group_id']]);
                    this.chart_obj.data.datasets[0].data.push(0);
                    this.chart_obj.data.datasets[1].data.push(parseInt(bonus_char_bar_stack[key]['profit']));
                    this.chart_obj.data.datasets[2].data.push(0);
                    this.chart_obj.data.labels.push(key);
                });﻿
                /*TODO:: 媒體公司顏色可以用什麼資料來分類*/
                Object.keys(media_companies_profit_data).forEach(key=>{
                    color.push( key%2 ? this.default_color.yellow : this.default_color.green);
                    this.chart_obj.data.datasets[2].data.push(0);
                    this.chart_obj.data.datasets[1].data.push(0);
                    this.chart_obj.data.datasets[0].data.push(  parseInt(media_companies_profit_data[key]['cost'].replace(/,/g,'')) );
                    this.chart_obj.data.labels.push(media_companies_profit_data[key]['name']);
                });﻿
                this.chart_obj.titleBlock.options.text = data.customer_profit_data[0].name;

                this.chart_obj.config.data.datasets[0].backgroundColor  = color;
                this.chart_obj.config.data.datasets[1].backgroundColor  = color;
                this.chart_obj.config.data.datasets[2].backgroundColor  = color;

                this.chart_obj.update({
                    duration: 700,
                    easing: 'linear'
                });
            },
        },
        watch: {
        }
    }
</script>

<style scoped>

</style>

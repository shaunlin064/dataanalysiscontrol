<template>
    <div class="box box-info" :id="'canvas-holder-'+table_id" :style="{
            'height': `${height ? height : 'auto'}px`
     }">
        <canvas :id=table_id></canvas>
    </div>
</template>

<script>
    import {mapState, mapMutations, mapActions} from 'vuex';
    import * as types from "../store/types";
    export default {
        name: "Chart",
        props: {
            table_id: String,
            title: String,
            type: String,
            chart_data: Array,
            labels: Array,
            ajax_url: String,
            csrf: String,
            height: Number,
        },
        computed: {
            ...mapState('chart',['month_income', 'month_cost', 'month_profit', 'money_status_paid', 'money_status_unpaid', 'money_status_bonus_paid', 'money_status_bonus_unpaid', 'month_label', 'last_record_month_income', 'last_record_month_cost', 'last_record_month_profit', 'last_record_month_label', 'chart_bar_max_y',]),
            ...mapState('financial',['provide_statistics_list','provide_total_money'])
        },
        data: function () {
            return {
                color:[
                    'rgba(142,197,252,0.5)',
                    'rgba(251,171,126,0.5)',
                    'rgba(255, 99, 132,0.5)',
                    'rgba(255, 159, 64,0.5)',
                    'rgba(255, 205, 86,0.5)',
                    'rgba(75, 192, 192,0.5)',
                    'rgba(54, 162, 235,0.5)',
                    'rgba(153, 102, 255,0.5)',
                    'rgba(201, 203, 207,0.5)'
                ],
                group_id_color:{
                    1:'rgba(142,197,252,0.5)',
                    2:'rgba(251,171,126,0.5)',
                    3:'rgba(255, 99, 132,0.5)',
                    4:'rgba(255, 159, 64,0.5)',
                    5:'rgba(201, 203, 207,0.5)',
                    6:'rgba(177,12,71,0.5)',
                    7:'rgba(111,222,170,0.5)',
                    8:'rgba(83,208,185,0.5)',
                    10:'rgba(75, 192, 192,0.5)',
                    11:'rgba(251,171,126,0.5)',
                    12:'rgba(255, 177, 145,0.5)',
                    13:'rgba(255, 183, 167,0.5)',
                    14:'rgba(142,197,252,0.5)',
                    15:'rgba(143, 171, 235,0.5)',
                    16:'rgba(149, 144, 213,0.5)',
                    17:'rgba(255, 99, 132,0.5)',
                    18:'rgba(216,62,100,0.5)',
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
                pie_color: [
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                ],
                bar_color: [
                    'rgba(255, 99, 132,0.5)',
                    'rgba(54, 162, 235,0.5)',
                    'rgba(75, 192, 192,0.5)',
                ],
                bar_label: [
                    '收入',
                    '成本',
                    '毛利'
                ],
                config: {
                    type: this.type,
                    data: {
                        'datasets': this.chart_data,
                        // 'datasets':[
                        //     {"data":[this.money_status_paid,this.money_status_unpaid,0,0]}
                        //     ,{"data":[0,0,this.money_status_bonus_paid,this.money_status_bonus_unpaid]}
                        // ],
                        'labels': this.labels,
                    },
                    options: {
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
                                    if(this.type == 'pie'){
                                        let percentage = (value*100 / sum).toFixed(1);
                                        if(percentage != '0.0'){
                                            return percentage+"%";
                                        }
                                    }else{
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
                                    }

                                    return null;
                                },
                                color: '#0c0b0b',
                            }
                        },
                        responsive: true,
                        maintainAspectRatio: this.height ? false : true,
                        title: {
                            display: true,
                            text: this.title
                        },
                        scales: {
                            xAxes: [{
                                stacked: true,
                                display: true,
                                ticks: {
                                    beginAtZero: true,
                                }
                            }],
                            yAxes: [{
                                stacked: true,
                                display: true,
                                ticks: {
                                    beginAtZero: true,
                                }
                            }]
                        },

                    },
                },
                chart_obj: {},
                chart_labels: [],
            }
        },
        created: function () {
            bus.$on('chartClear',this.chartClear);
            let vue_this = this;
            this.config.data.datasets.map(function (v, k, i) {
                if (vue_this.type == 'pie') {
                    v.backgroundColor = vue_this.pie_color;
                } else if (vue_this.type == 'bar') {
                    v.label = vue_this.bar_label[k];
                    v.backgroundColor = vue_this.bar_color[k];
                    v.stack = `${k}`;
                }
            });

        },
        mounted: function () {
            var ctx = document.getElementById(this.table_id).getContext('2d');
            this.chart_obj = new Chart(ctx, this.config);

        },
        methods: {
            getRandom(min, max) {
                return Math.floor(Math.random() * (max - min + 1)) + min;
            },
            chartClear(){
                let vue = this;
                this.chart_obj.data.labels = [];
                this.chart_obj.data.datasets.map( (v,k) =>{
                    vue.chart_obj.data.datasets[k]['data'] = [];
                    vue.chart_obj.data.datasets[k]['backgroundColo']=[];
                });
                this.chart_obj.update();
            },
            update(vue_this) {
                if (vue_this.type == 'pie') {
                    vue_this.chart_obj.data.datasets.map(function (dataset, key) {

                        if (key == 0) {
                            dataset.data = [vue_this.money_status_unpaid, vue_this.money_status_paid, 0, 0];
                        }
                    });
                    if (vue_this.table_id == 'chart_provide_pie') {
                        let data = [];
                        let userdata = [];
                        let groupdata = [];
                        vue_this.chart_obj.data.labels = [];
                        vue_this.chart_obj.data.datasets[0].backgroundColor=[];
                        vue_this.chart_obj.data.datasets[1].backgroundColor=[];

                        Object.keys(vue_this.provide_statistics_list.user).forEach(key => {
                            let money = vue_this.provide_statistics_list.user[key]['money'];
                            let group_id = vue_this.provide_statistics_list.user[key]['group_id'];

                            let color = vue_this.group_id_color[group_id];
                            userdata.push(money);
                            vue_this.chart_obj.data.datasets[0].backgroundColor.push(color);
                            vue_this.chart_obj.data.datasets[1].backgroundColor.push(color);
                            groupdata.push(0);
                            vue_this.chart_obj.data.labels.push(key);
                        });

                        Object.keys(vue_this.provide_statistics_list.group).forEach(key => {
                            let money = vue_this.provide_statistics_list.group[key]['money'];
                            let group_id = vue_this.provide_statistics_list.group[key]['group_id'];
                            let color = vue_this.group_id_color[group_id];
                            userdata.push(0);
                            groupdata.push(money);
                            vue_this.chart_obj.data.labels.push(key);
                            vue_this.chart_obj.data.datasets[0].backgroundColor.push(color);
                            vue_this.chart_obj.data.datasets[1].backgroundColor.push(color);
                        });

                        data.push(userdata);
                        data.push(groupdata);

                        vue_this.chart_obj.data.datasets.map(function (dataset, key) {
                            dataset.data = data[key];
                        });
                    }
                } else if (vue_this.type == 'bar') {
                    if (vue_this.table_id == 'chart_financial_bar_last_record') {
                        let monthdata = [
                            vue_this.last_record_month_income,
                            vue_this.last_record_month_cost,
                            vue_this.last_record_month_profit,
                        ];
                        vue_this.chart_obj.data.labels = vue_this.last_record_month_label;
                        vue_this.chart_obj.data.datasets.map(function (dataset, key) {
                            dataset.data = monthdata[key];
                        });

                    } else if (vue_this.table_id == 'chart_provide_bar') {
                        let data = [];
                        let newdata = [];
                        vue_this.chart_obj.data.labels = [];
                        Object.keys(vue_this.provide_statistics_list.user).forEach(key => {
                            let money = vue_this.provide_statistics_list.user[key]['money'];
                            newdata.push(money);
                            vue_this.chart_obj.data.labels.push(key);
                        });
                        this.config.data.datasets[0].backgroundColor = vue_this.bar_color[2];
                        data.push(newdata);

                        vue_this.chart_obj.data.datasets.map(function (dataset, key) {
                            dataset.data = data[key];
                        });
                    } else {
                        let monthdata = [
                            vue_this.month_income,
                            vue_this.month_cost,
                            vue_this.month_profit,
                        ];
                        vue_this.chart_obj.data.labels = vue_this.month_label;
                        vue_this.chart_obj.data.datasets.map(function (dataset, key) {
                            dataset.data = monthdata[key];
                        });
                    }
                    /*update yAxes max 確保比對圖表兩邊比例一樣 */
                    if ($.inArray(this.table_id, ['chart_financial_bar2', 'chart_financial_bar_last_record']) !== -1) {
                        vue_this.config.options.scales.yAxes[0].ticks.max = vue_this.chart_bar_max_y;
                    }

                    vue_this.chart_obj.options = vue_this.chart_obj.config.options;

                }
                vue_this.chart_obj.update({
                    duration: 700,
                    easing: 'linear'
                });
            },
            ...mapActions({
                saveName: 'saveName'
            }),
        },
        watch: {
            money_status_bonus_paid: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined) {
                        this.update(this);
                    }
                }
            },
            money_status_bonus_unpaid: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined) {
                        this.update(this);
                    }
                }
            },
            money_status_unpaid: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined) {
                        this.update(this);
                    }
                }
            },
            money_status_paid: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined) {
                        this.update(this);
                    }
                }
            },
            month_income: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined) {
                        this.update(this);
                    }
                }
            },
            month_cost: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined) {
                        this.update(this);
                    }
                }
            },
            provide_total_money: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== oldVal) {
                        this.update(this);
                    }
                }
            },
        }
    }
</script>

<style scoped>

</style>

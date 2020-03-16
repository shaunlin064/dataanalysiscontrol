<template>
    <div class="box box-info" :id="'canvas-holder-'+table_id" :style="{
            'height': `${height ? height : 'auto'}px`
     }">
        <canvas :id=table_id></canvas>
    </div>
</template>

<script>
    import {mapState, mapMutations, mapActions} from 'vuex';

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
        computed: {...mapState(['month_income', 'month_cost', 'month_profit', 'money_status_paid', 'money_status_unpaid', 'money_status_bonus_paid', 'money_status_bonus_unpaid', 'month_label', 'last_record_month_income', 'last_record_month_cost', 'last_record_month_profit', 'last_record_month_label', 'chart_bar_max_y', 'provide_statistics_list', 'provide_total_money'])},
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
                    5:'rgba(201, 203, 207,0.5)'
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
                    }
                },
                chart_obj: {},
                chart_labels: [],
            }
        },
        created: function () {
            let vue_this = this;
            // var default_color = vue_this.default_color;
            this.config.data.datasets.map(function (v, k, i) {

                if (vue_this.type == 'pie') {
                    v.backgroundColor = vue_this.pie_color;
                } else if (vue_this.type == 'bar') {
                    v.label = vue_this.bar_label[k];
                    v.backgroundColor = vue_this.bar_color[k];
                    // if(k <= 1){
                    //     v.stack = '0';
                    // }else{
                    //     v.stack = '1';
                    // }
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

                        vue_this.provide_statistics_list.user = vue_this.provide_statistics_list.user.sort(function (a, b) {
                            if(a.group_id < b.group_id){
                                return 1;
                            }
                            if(a.group_id > b.group_id){
                                return -1;
                            }
                            return 0;
                        });
                        vue_this.provide_statistics_list.group = vue_this.provide_statistics_list.group.sort(function (a, b) {
                            return a.group_id < b.group_id ? 1 : -1;
                        });

                        function getSort(datas){
                            let v1 = {};
                            let v2 = datas;
                            let useKey = 'group_id';
                            Object.keys(v2).forEach( key => {
                                let keyValue = v2[key][useKey];
                                if(v1[keyValue] === undefined){
                                    v1[keyValue] = [];
                                }
                                if(v1[keyValue][key] === undefined){
                                    v1[keyValue][key] = [];
                                }
                                v1[keyValue][key].push(v2[key]);
                            },useKey);

                            let v3 = [];
                            Object.keys(v1).forEach(key=>{
                                let items = v1[key];
                                Object.keys(items).forEach( key => {
                                    if(v3[key] === undefined){
                                        v3[key] = [];
                                    }
                                    v3[key] = items[key][0];
                                });
                            });
                            return v3;
                        }


                        vue_this.provide_statistics_list.user = getSort(vue_this.provide_statistics_list.user);
                        // console.log( vue_this.provide_statistics_list.user);
                        Object.keys(vue_this.provide_statistics_list.user).forEach(key => {
                            let money = vue_this.provide_statistics_list.user[key]['money'];
                            let group_id = vue_this.provide_statistics_list.user[key]['group_id'];
                            // let label = vue_this.provide_statistics_list.user[key]['name'];
                            let color = vue_this.group_id_color[group_id];
                            userdata.push(money);
                            vue_this.chart_obj.data.datasets[0].backgroundColor.push(color);
                            vue_this.chart_obj.data.datasets[1].backgroundColor.push(color);
                            groupdata.push(0);
                            vue_this.chart_obj.data.labels.push(key);
                        });

                        vue_this.provide_statistics_list.group = getSort(vue_this.provide_statistics_list.group);
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
            }
        }
    }
</script>

<style scoped>

</style>

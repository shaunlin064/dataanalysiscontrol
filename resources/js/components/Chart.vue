<template>
    <div class="box box-info" id="canvas-holder" :style="{
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
            height:Number
        },
        computed: {...mapState(['month_income', 'month_cost', 'month_profit', 'money_status_paid', 'money_status_unpaid', 'money_status_bonus_paid', 'money_status_bonus_unpaid','month_label','last_record_month_income','last_record_month_cost', 'last_record_month_profit','last_record_month_label','chart_bar_max_y'])},
        data: function () {
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
                pie_color: [
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
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
                            yAxes: [{
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
            update(vue_this) {
                if (vue_this.type == 'pie') {
                    vue_this.chart_obj.data.datasets.map(function (dataset, key) {
                        
                        if (key == 0) {
                            dataset.data = [vue_this.money_status_unpaid, vue_this.money_status_paid, 0, 0];
                        }
                        // else{
                        //     dataset.data = [0,0,vue_this.money_status_bonus_paid,vue_this.money_status_bonus_unpaid];
                        // }
                    });
                } else if (vue_this.type == 'bar') {
                    if(vue_this.table_id == 'chart_financial_bar_last_record'){
                        let monthdata = [
                            vue_this.last_record_month_income,
                            vue_this.last_record_month_cost,
                            vue_this.last_record_month_profit,
                        ];
                        vue_this.chart_obj.data.labels = vue_this.last_record_month_label;
                        vue_this.chart_obj.data.datasets.map(function (dataset, key) {
                            dataset.data = monthdata[key];
                        });

                    }else{
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
                    vue_this.config.options.scales.yAxes[0].ticks.max = vue_this.chart_bar_max_y;
                    vue_this.chart_obj.options = vue_this.chart_obj.config.options;
                    
                }
                vue_this.chart_obj.update({
                    duration: 700,
                    easing: 'linear'});
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
            }
        }
    }
</script>

<style scoped>

</style>

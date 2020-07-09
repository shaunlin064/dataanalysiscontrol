<template>
    <div class="box box-info" id="canvas-holder">
        <canvas :id=table_id></canvas>
    </div>
</template>

<script>
    import {mapState, mapMutations, mapActions} from 'vuex';
    import * as types from "../store/types";

    export default {
        name: "CustomerProfitBar",
        props: {
            table_id: String,
            title: String,
            labels: Array
        },
        computed: {...mapState('chart',['agency_profit','client_profit','month_label','color','group_id_color','default_color'])},
        data: function () {
            return {
                config: {
                    type: 'bar',
                    data: {
                        'datasets': [
                            {"data":0},
                            {"data":0},
                            {type:"line",
                                fill:false,
                                borderColor: 'rgba(75, 192, 192,0.5)',
                                borderWidth: 2,
                                "data": 0}
                        ],
                        'labels': this.month_label,
                    },
                    options: {
                        responsive: true,
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
                        plugins: {
                            labels:
                                {
                                    render: function (args) {
                                        return new Intl.NumberFormat().format(args.value);
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
            this.config.data.datasets.map(function (v, k, i) {
                    v.label = vue_this.labels[k];
                    let color;
                    switch(k){
                        case 0 :
                            color = vue_this.default_color.red;
                            break;
                        case 1 :
                            color = vue_this.default_color.blue;
                            break;
                        case 2 :
                            color =vue_this.default_color.green;
                            break;
                    }
                    v.backgroundColor = color;
                    v.stack = `${k}`;
            });
        },
        mounted: function () {
            var ctx = document.getElementById(this.table_id).getContext('2d');
            this.chart_obj = new Chart(ctx, this.config);
        },
        methods: {
            update(vue_this) {
                    let totalProfit = [];

                    for (var i = 0; i < vue_this.agency_profit.length; i++) {
                        totalProfit[i] = vue_this.agency_profit[i]+vue_this.client_profit[i];
                    };

                    let monthdata = [
                        vue_this.agency_profit,
                        vue_this.client_profit,
                        totalProfit
                    ];
                    vue_this.chart_obj.data.labels = vue_this.month_label;
                    vue_this.chart_obj.data.datasets.map(function (dataset, key) {
                        dataset.data = monthdata[key];
                    });

                vue_this.chart_obj.update();
            },
        },
        watch: {
            agency_profit: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined) {
                        this.update(this);
                    }
                }
            },
            client_profit: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined ) {
                        this.update(this);
                    }
                }
            },
        }
    }
</script>

<style scoped>

</style>

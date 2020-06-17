<template>
    <div class="box box-info" id="canvas-holder">
        <canvas :id=table_id></canvas>
    </div>
</template>

<script>
    import {mapState, mapMutations, mapActions} from 'vuex';

    export default {
        name: "CustomerProfitBar",
        props: {
            table_id: String,
            title: String,
            labels: Array
        },
        computed: {...mapState(['agency_profit','client_profit','month_label'])},
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
                color: [
                    'rgba(255, 99, 132,0.5)',
                    'rgba(54, 162, 235,0.5)',
                    'rgba(75, 192, 192,0.5)',
                ],
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
                        'labels': this.$store.state.month_label,
                    },
                    options: {
                        responsive: true,
                        title: {
                            display: true,
                            text: this.title
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
                    v.backgroundColor = vue_this.color[k];
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
                    vue_this.chart_obj.data.labels = vue_this.$store.state.month_label;
                    vue_this.chart_obj.data.datasets.map(function (dataset, key) {
                        dataset.data = monthdata[key];
                    });

                vue_this.chart_obj.update();
            },
            ...mapActions({
                saveName: 'saveName'
            }),
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

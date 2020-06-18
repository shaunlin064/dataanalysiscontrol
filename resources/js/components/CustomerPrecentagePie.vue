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
            title: String,
            labels: Array,
        },
        computed: {
            ...mapState('chart', ['agency_profit', 'client_profit', 'sale_channel_profitData'])
        },
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
                    'rgba(255, 205, 86,0.5)',
                    'rgba(75, 192, 192,0.5)',
                    'rgba(153, 102, 255,0.5)',
                ],
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
                                align: 'bottom',
                                formatter: (value, ctx) => {
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
                                    if (percentage != '0.0') {
                                        return percentage + "%";
                                    }
                                    return null;

                                },
                                color: '#0c0b0b',
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
                v.backgroundColor = vue_this.color;
            });
        },
        mounted: function () {
            var ctx = document.getElementById(this.table_id).getContext('2d');
            this.chart_obj = new Chart(ctx, this.config);
        },
        methods: {
            update(vue_this) {
                vue_this.chart_obj.data.datasets[0].data = [
                    vue_this.agency_profit.reduce((a, b) => a + b),
                    vue_this.client_profit.reduce((a, b) => a + b),
                    0,
                    0,
                    0
                ];
                vue_this.chart_obj.data.datasets[1].data = [
                    0,
                    0,
                    vue_this.sale_channel_profitData['AP'],
                    vue_this.sale_channel_profitData['BR'],
                    vue_this.sale_channel_profitData['EC']
                ];
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
                    if (oldVal !== undefined) {
                        this.update(this);
                    }
                }
            },
        }
    }
</script>

<style scoped>

</style>

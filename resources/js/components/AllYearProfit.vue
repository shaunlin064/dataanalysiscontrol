<template>
    <div class="box box-info" id="canvas-holder">
        <canvas :id=table_id></canvas>
    </div>
</template>

<script>
    import {mapState, mapMutations, mapActions} from 'vuex';
    import * as types from "../store/types";
    export default {
        name: "AllYearProfit",
        props: {
            table_id: String,
            title: String,
            datas: Object,
        },
        data: function () {
            return {
                default_color: [
                    'rgba(255, 99, 132,0.5)',
                    'rgba(255, 159, 64,0.5)',
                    'rgba(75, 192, 192,0.5)',
                    'rgba(54, 162, 235,0.5)',
                    'rgba(153, 102, 255,0.5)',
                    'rgba(201, 203, 207,0.5)',
                    'rgba(156, 184, 222,0.5)',
                    'rgba(60, 180, 66,0.5)',
                    'rgba(33, 22, 69,0.5)',
                ],
                config: {
                    type: 'line',
                    data: {
                        'datasets': [
                        ],
                        'labels': this.datas.stack,
                    },
                    options: {
                        responsive: true,
                        title: {
                            display: true,
                            text: this.title
                        },
                    }
                },
                chart_obj: {},
                chart_labels: [],
            }
        },
        created: function () {
            const entries = Object.entries(this.datas.data);
            let colorNumber = 0;

            for (const [label, value] of entries) {
                const color = this.default_color[colorNumber++];

                this.config.data.datasets.push({
                    'backgroundColor': color,
                    'borderColor':  color,
                    'label': label,
                    'data': value,
                    'fill' : false
                    });
            }
        },
        methods: {
        },
        mounted: function () {
            var ctx = document.getElementById(this.table_id).getContext('2d');
            this.chart_obj = new Chart(ctx, this.config);
        }
    }
</script>

<style scoped>

</style>

<template>
    <div class="box box-info" :id="'canvas-holder-'+table_id" :style="{
            'height': `${height ? height : 'auto'}px`
     }">
        <canvas :id=table_id></canvas>
    </div>
</template>

<script>
    import {mapState, mapMutations, mapActions, mapGetters} from 'vuex';

    export default {
        name: "chart_stack",
        props: {
            table_id: String,
            title: String,
            height: Number,
            row: Array,
        },
        data() {
            return {
                color: [
                    'rgba(142,197,252,0.5)',
                    'rgba(251,171,126,0.5)',
                    'rgba(255, 99, 132,0.5)',
                    'rgba(255, 159, 64,0.5)',
                    'rgba(255, 205, 86,0.5)',
                    'rgba(75, 192, 192,0.5)',
                    'rgba(54, 162, 235,0.5)',
                    'rgba(153, 102, 255,0.5)',
                    'rgba(201, 203, 207,0.5)',
                    'rgba(229,210,195,0.5)',
                    'rgba(204,176,172,0.5)',
                    'rgba(173,145,9,0.5)',
                    'rgba(134,118,137,0.5)',
                    'rgba(90,95,116,0.5)',
                    'rgba(47,72,88,0.5)',
                    'rgba(72,90,115,0.5)',
                    'rgba(103,106,139,0.5)',
                    'rgba(140,122,159,0.5)',
                    'rgba(179,138,174,0.5)',
                    'rgba(218,155,183,0.5)',
                    'rgba(251,162,174,0.5)',
                ],
                group_id_color: {
                    1: 'rgba(142,197,252,0.5)',
                    2: 'rgba(251,171,126,0.5)',
                    3: 'rgba(255, 99, 132,0.5)',
                    4: 'rgba(255, 159, 64,0.5)',
                    5: 'rgba(201, 203, 207,0.5)'
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
                config: {
                    type: 'bar',
                    data: {
                        'datasets': [],
                        'labels': [],
                    },
                    options: {
                        maintainAspectRatio: this.height ? false : true,
                        title: {
                            display: true,
                            text: this.title
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: false
                        },
                        responsive: true,
                        scales: {
                            xAxes: [{
                                stacked: true,
                            }],
                            yAxes: [{
                                stacked: true,
                            }]
                        },
                    }
                }
            }
        },
        computed: {
            ...mapState(['provide_total_money','provide_groups_list', 'provide_bonus_list','provide_char_bar_stack']),
        },
        created: function () {},
        mounted: function () {
            var ctx = document.getElementById(this.table_id).getContext('2d');
            this.chart_obj = new Chart(ctx, this.config);
        },
        methods: {
            update() {
                /*資料整理*/
                let barChartData = this.trimData();
                this.chart_obj.data = barChartData;
                this.chart_obj.update({
                    duration: 700,
                    easing: 'linear'
                });
            },
            trimData(){

                let barChartData = {};
                let trimDatas = [];
                let originalData = this.provide_char_bar_stack;
                let stackLabel = [];
                let columnLabel = [];
                var vue = this;
                let i = 0;

                Object.keys(originalData).forEach(date=>{
                    // trimDatas[key] = [];
                    let items = [];
                    let datas = originalData[date];
                    stackLabel.push(date);

                    Object.keys(datas).forEach(userName =>{
                        if(i == 0){
                            columnLabel.push(userName);
                        }
                        items.push(datas[userName]['provide_money']);
                    },columnLabel,i);

                    trimDatas.push({
                        label:date,
                        data:items,
                        stack:'0',
                        backgroundColor:vue.color[i]
                    });
                    i++;

                },trimDatas,stackLabel,columnLabel,i);
                barChartData.labels = columnLabel;
                barChartData.datasets = trimDatas;

                return barChartData;
            }
        },
        beforeMount: function () {

        },
        watch: {
            provide_total_money: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined) {
                        this.update();
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>

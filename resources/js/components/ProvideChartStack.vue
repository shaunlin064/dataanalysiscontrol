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
                    'rgba(177,12,71,0.5)',
                    'rgba(111,222,170,0.5)',
                    'rgba(83,208,185,0.5)',
                    'rgba(75, 192, 192,0.5)',
                    'rgba(251,171,126,0.5)',
                    'rgba(255, 177, 145,0.5)',
                    'rgba(255, 183, 167,0.5)',
                    'rgba(142,197,252,0.5)',
                    'rgba(143, 171, 235,0.5)',
                    'rgba(149, 144, 213,0.5)',
                    'rgba(255, 99, 132,0.5)',
                    'rgba(216,62,100,0.5)',
                    'rgba(90,95,116,0.5)',
                    'rgba(201, 203, 207,0.5)',
                    'rgba(229,210,195,0.5)',
                    'rgba(204,176,172,0.5)',
                    'rgba(173,145,9,0.5)',
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
            ...mapState('financial',['provide_total_money','provide_groups_list', 'provide_bonus_list','provide_char_bar_stack']),
        },
        created: function () {
            bus.$on('chartStackClear',this.chartStackClear);
        },
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
            chartStackClear(){
                let vue = this;
                this.chart_obj.data.labels = [];
                this.chart_obj.data.datasets.map( (v,k) =>{
                    vue.chart_obj.data.datasets[k]['data'] = [];
                    vue.chart_obj.data.datasets[k]['backgroundColo']=[];
                });
                this.chart_obj.update();
            },
            trimData(){
                var vue = this;
                let barChartData = {};
                let trimDatas = [];
                let originalData = this.provide_char_bar_stack;
                let stackDateLabel = [];
                let columnUserNameLabel = [];
                let newSortDatas = {};
                let dateSortArray = Object.entries(originalData); //把 obj 拆成 array
                dateSortArray = dateSortArray.map( (a) => a[0]).sort();

                 dateSortArray.map((date) => {
                     newSortDatas[date] = originalData[date];
                },originalData,newSortDatas)
                /*get All User name include datas*/
                Object.keys(newSortDatas).forEach(date=>{
                    Object.keys(newSortDatas[date]).forEach(userName =>{
                        if($.inArray(userName,columnUserNameLabel) === -1){
                            columnUserNameLabel.push(userName);
                        }
                    },columnUserNameLabel);
                },columnUserNameLabel);

                Object.keys(newSortDatas).forEach(date=>{
                    let items = [];
                    let datas = newSortDatas[date];
                    stackDateLabel.push(date);

                    Object.keys(datas).forEach(userName =>{
                        if(items.length === 0){
                            columnUserNameLabel.map(()=>{
                                items.push(0);
                            },items);
                        }
                        let index = $.inArray(userName,columnUserNameLabel);
                        items[index] = datas[userName]['provide_money'];
                    },columnUserNameLabel);

                    let colorNumber = parseInt(date.substr(5,6))%12;
                    trimDatas.push({
                        label:date,
                        data:items,
                        stack:'0',
                        backgroundColor:vue.color[colorNumber]
                    });
                },trimDatas,stackDateLabel,columnUserNameLabel);

                barChartData.labels = columnUserNameLabel;
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

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
            ...mapState('chart',['color','group_id_color', 'default_color']),
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
                    vue.chart_obj.data.datasets[k]['backgroundColor']=[];
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

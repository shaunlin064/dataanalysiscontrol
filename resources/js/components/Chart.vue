<template>
	<div class="box box-danger" id="canvas-holder">
		<canvas :id=dom_id></canvas>
	</div>
</template>

<script>
    import {mapState,mapMutations,mapActions} from 'vuex';
    export default {
        name: "Chart",
		    props:{
            dom_id:String,
				    title:String,
				    type:String,
				    chart_data:Array,
            labels:Array
		    },
        computed: mapState(['month_income','month_cost','money_status_paid','money_status_unpaid','money_status_bonus_paid','money_status_bonus_unpaid']),
		    data: function(){
            return {
                default_color:{
                  red: 'rgb(255, 99, 132)',
                  orange: 'rgb(255, 159, 64)',
                  yellow: 'rgb(255, 205, 86)',
                  green: 'rgb(75, 192, 192)',
                  blue: 'rgb(54, 162, 235)',
                  purple: 'rgb(153, 102, 255)',
                  grey: 'rgb(201, 203, 207)'
	            },
	            pie_color:[
                  'rgb(255, 205, 86)',
                  'rgb(75, 192, 192)',
                  'rgb(255, 205, 86)',
                  'rgb(75, 192, 192)',
	            ],
	            bar_color:[
                  'rgb(255, 99, 132)',
                  'rgb(54, 162, 235)',
                  'rgb(75, 192, 192)',
	            ],
	            bar_label:[
	                '收入',
			            '成本',
			            '毛利'
	            ],
					    config: {
	                type: this.type,
	                data: {
                      'datasets':this.chart_data,
                      // 'datasets':[
                      //     {"data":[this.money_status_paid,this.money_status_unpaid,0,0]}
                      //     ,{"data":[0,0,this.money_status_bonus_paid,this.money_status_bonus_unpaid]}
                      // ],
			                'labels' : this.labels,
	                },
	                options: {
	                    responsive: true,
	                    title: {
	                        display: true,
	                        text: this.title
	                    },
	                }
	            },
	            chart_obj :{}
            }
		    },
        created: function(){
            let vue_this = this;
            // var default_color = vue_this.default_color;
            this.config.data.datasets.map( function(v,k,i){
                
                if(vue_this.type == 'pie'){
                    v.backgroundColor = vue_this.pie_color;
                }else if (vue_this.type == 'bar'){
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
		    mounted: function(){
            var ctx = document.getElementById(this.dom_id).getContext('2d');
            this.chart_obj = new Chart(ctx, this.config);
		    },
		    methods:{
            update(vue_this){

                if(vue_this.type == 'pie'){
                    vue_this.chart_obj.data.datasets.map( function(dataset,key){
                        if(key == 0){
                            dataset.data = [vue_this.money_status_paid,vue_this.money_status_unpaid,0,0];
                        }else{
                            dataset.data = [0,0,vue_this.money_status_bonus_paid,vue_this.money_status_bonus_unpaid];
                        }
                    });
                }else if(vue_this.type == 'bar'){
                    let monthdata = [
                        vue_this.month_income,
		                    vue_this.month_cost,
                        vue_this.month_income-vue_this.month_cost,
                    ];
                    vue_this.chart_obj.data.datasets.map( function(dataset,key){
		                    dataset.data = [monthdata[key]];
                    });
                }

                vue_this.chart_obj.update();
            },
            ...mapActions({
                saveName: 'saveName'
            }),
		    },
        watch:{
            money_status_paid: {
                immediate: true,
                handler (val, oldVal) {
                    if(oldVal !== undefined){
                        this.update(this);
                    }
                }
            },
            month_income:{
                immediate: true,
                handler (val, oldVal) {
                    if(oldVal !== undefined){
                        this.update(this);
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>

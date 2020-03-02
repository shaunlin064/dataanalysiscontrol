<template>
	<div class="input-group date">
		<div class="input-group-addon">
			<i class="fa fa-calendar"></i>
		</div>
		<input type="text" class="form-control pull-right" :id=dom_id :value=date :name=dom_id>
	</div>
</template>

<script>
    import {mapState,mapMutations,mapActions} from 'vuex';
    export default {
        name: "Datepicker",
		    props:{
            dom_id:String,
            date : String
		    },
        data: function() {
            return {
                date_start: this.date ? this.date : new Date(),
            }
        },
        methods:{
            changeDate(value){
                this.$store.commit('changeDate',value);
            },
            getrand(flot){
                return Math.round(Math.random() * flot);
            },
            changeemit(value){
                this.$store.commit('changeDate',value);
                // this.$store.commit('changeMoneyStatus',{'paid':this.getrand(1000),'unpaid':this.getrand(1000),'bPaid':this.getrand(1000),'bUnPaid':this.getrand(100)});
                // this.$store.commit('changeMonthBalancen',{'month_income':this.getrand(1000),'month_cost':this.getrand(1000)});
                // this.$store.commit('changeBox',{'profit':this.getrand(1000),'bonus_rate':this.getrand(100),'bonus_next_amount':this.getrand(1000),'bonus_next_percentage':this.getrand(100)});
            },
        },
        created: function(){
            // let domEle = $('#'+this.dom_id+'');
            let d = new Date(this.date);
						this.changeDate(moment(d).format('YYYY/MM'));
            // domEle.val(moment(d).format('YYYY/MM'));
        },
		    mounted: function() {
            let vue = this;
            let d = this.date ? this.date :this.date_start;
            let m = moment(d).format('MM');
            let y = moment(d).format('YYYY');
            let td = moment(d).format('DD');
            
            let domEle = $('#'+this.dom_id+'');
            
            domEle.datepicker({
                autoclose: true,
                startView: "months",
                minViewMode: "months",
                defaultViewDate: {
                    year: y,
                    month: m,
                    day: td
                },
                format: 'yyyy/mm',
                language: 'zh-TW',
            }).on("change", function() {
                vue.changeemit(this.value);
            });
            
        }
    }
</script>

<style scoped>

</style>

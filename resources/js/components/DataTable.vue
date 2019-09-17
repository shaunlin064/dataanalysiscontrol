<template>
	<div class="box">
		<div class="box-header">
			<h3 class="box-title"> {{ box_title }}</h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<table :id=dom_id class="table table-bordered table-striped">
				<thead>
				<tr>
					<th v-for='item in data_table.rowTitle'>{{item}}</th>
				</tr>
				</thead>
				<tbody>
				</tbody>
				<tfoot>
				<tr>
					<th v-for='item in data_table.rowTitle'>{{item}}</th>
				</tr>
				</tfoot>
			</table>
		</div>
		<!-- /.box-body -->
	</div>
</template>

<script>
	
	
  // require('../../../public/adminLte_componets/bootstrap-daterangepicker/daterangepicker');
    import {mapState,mapMutations,mapActions} from 'vuex';
    export default {
        name: "DataTable",
		    props: {
            dom_id: String,
				    box_title: String,
				    data_table: Object
		    },
        computed: mapState(['change_date','user_id']),
        methods:{
            getCampaignData(uId,dateYearMonth){
                // dateYearMonth =  dateYearMonth.replace('/','');
                axios.post('/bonus/review/getdata',{
                  uId:uId,dateYearMonth:dateYearMonth
                }).then(
                    response => {
                        let chartDataBar = response.data.chartDataBar;
		                    let boxData = response.data.boxData;
		                    let chartMoneyStatus = response.data.chartMoneyStatus;
                        this.updataTable(response.data.erpReturnData);

                        this.$store.commit('changeMonthBalancen',{'month_income':chartDataBar.totalIncome,'month_cost':chartDataBar.totalCost});
										
                        this.$store.commit('changeBox',{value:boxData.profit,field:'profit'});
                        this.$store.commit('changeBox',{value:boxData.bonus_rate,field:'bonus_rate'});
                        this.$store.commit('changeBox',{value:boxData.bonus_next_amount,field:'bonus_next_amount'});
                        this.$store.commit('changeBox',{value:boxData.bonus_next_percentage,field:'bonus_next_percentage'});
                        this.$store.commit('changeBox',{value:boxData.bonus_direct,field:'bonus_direct'});

                        this.$store.commit('changeMoneyStatus',{paid:chartMoneyStatus.paid,unpaid:chartMoneyStatus.unpaid});
		                    // console.log(response.data.erpReturnData);
                    },
                    err => {
                        reject(err);
                    }
                );
            },
		        updataTable(row){
                this.dataTable.clear();
                this.dataTable.rows.add( row );
                this.dataTable.draw();
                // console.log(row);
		        }
        },
        mounted: function(){
            // for((item,index)  in this.title){
            //     console.log(item,index);
            // }
            if(this.$attrs.user_id !== undefined){
                this.$store.commit('changeUserId',this.$attrs.user_id);
            }
            
		        let columns = [];
            
            Object.keys(this.data_table.rowTitle).forEach(function(v,k,i){
		            
                  columns.push(
                      {
                          data:v
                      });
            });
            this.dataTable = $('#'+this.dom_id+'').DataTable({
                'paging'      : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : false,
                "scrollX": true,
                "aLengthMenu" : [25, 50, 100], //更改顯示記錄數選項
                "oLanguage": {
                    "emptyTable"    : "目前沒有任何（匹配的）資料。",
                    "sProcessing":   "處理中...",
                    "sLengthMenu":   "顯示 _MENU_ 項結果",
                    "sZeroRecords":  "沒有資料",
                    "sInfo":         "顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項",
                    "sInfoEmpty":    "顯示第 0 至 0 項結果，共 0 項",
                    "sInfoFiltered": "(從 _MAX_ 項結果過濾)",
                    "sInfoPostFix":  "",
                    "sSearch":       "搜索:",
                    "sUrl":          "",
                    "oPaginate": {
                        "sFirst":    "首頁",
                        "sPrevious": "上頁",
                        "sNext":     "下頁",
                        "sLast":     "尾頁"
                    }
                },
                columns: columns,
            });
            this.dataTable.clear();
            this.dataTable.rows.add( this.data_table.data );
            this.dataTable.draw();
            // console.log(this.data_table.data);
        },
        watch:{
            change_date: {
                immediate: true,    // 这句重要
                handler (val, oldVal) {
                    if(oldVal !== undefined) {
                        this.getCampaignData(this.user_id,val);
                        console.log(val);
                    }
                }
            }
		    }
    }
</script>

<style>
	
</style>

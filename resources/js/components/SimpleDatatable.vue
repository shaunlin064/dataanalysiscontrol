<template>
	<div class="form-group">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">{{table_head}}</h3>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<table :id='table_id' class="table table-bordered table-striped" >
						<thead>
						<tr>
							<th v-for='item in table_title'>{{item}}</th>
						</tr>
						</thead>
						<tbody>
						</tbody>
						<tbody id="content_loader" v-if='loading'>
						<tr>
							<td colspan="14" class='text-center'>
								<i class="fa fa-spin fa-refresh" style="font-size: 3em; padding: 20px;"></i>
							</td>
						</tr>
						</tbody>
						<tfoot>
						<tr>
							<th v-for='item in table_title'>{{item}}</th>
						</tr>
						</tfoot>
					</table>
				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->
		<!-- /.col -->
	</div>
</template>

<script>
    import {mapState,mapMutations,mapActions,mapGetters} from 'vuex';
    export default {
        name: "SimpleDatatable",
        props: {
            table_id : String,
            table_head : String,
		        table_title : Array,
		        row : Array,
            columns : Array,
            ex_buttons : Array,
		        type: String,
            csrf: String,
		        ajax_url: String,
            length_change: String,
		        page_length: Number,
            select_id:Array,
		        total_money:Number
        },
        data() {
            return {
              now : 0,
            }
        },
        computed: {
            // ...mapGetters(['getTableSelect','getUserIds','getSaleGroupIds','getStartDate','getEndDate']),
		        ...mapState(['provide_money','table_select','start_date','end_date','change_date','user_ids','sale_group_ids','loading']),
        },
		    methods:{
            change_render(item){
                return new Function('data','type','row', 'return `'+item+'`');
            },
            set_select(value) {
                this.$store.commit('tableSelect', value);
            },
				    tableClear(){
                if(this.dataTable !== undefined){
                    this.dataTable.clear();
                    this.dataTable.draw();
                }
				    },
		        updataTable(row){
                if(this.dataTable !== undefined){
                this.dataTable.clear();
		            this.dataTable.rows.add( row );
                this.dataTable.draw();
                }
		        },
				    getData(){
						    if(this.ajax_url === undefined){
						        return false;
						    }
						    let data = {
                    _token : this.csrf,
						        startDate : this.$store.state.start_date,
                    endDate : this.$store.state.end_date,
                    saleGroupIds : this.$store.state.sale_group_ids,
                    userIds : this.$store.state.user_ids
						    };
						    
                if( (data.saleGroupIds == '' && data.userIds == '') || data._token === undefined ){
									return false;
                }
                var table_id = this.table_id;
                this.$store.state.loading = true;
                this.tableClear();
                axios.post(this.ajax_url,data).then(
                    response => {
                        this.$store.state.loading = false;
                        let rowData = eval(`response.data.${this.table_id}`);
                        
                        let total = parseInt(0);
		                    
                        if(rowData){
                            
                            rowData.map(function(v){
                                total += parseInt(v.provide_money);
                            });
                            
                            if(table_id == 'provide_bonus_list'){
                                this.$store.state.bonus_total_money = total;
                            }else{
                                this.$store.state.sale_group_total_money = total;
                            }

                            this.updataTable(rowData);
                        };
                        
                    },
                    err => {
                        
                        reject(err);
                    }
                );
                
				    },
            getExportFileName(){
                return `${this.table_head}_${this.start_date.substr(0,7)}-${this.end_date.substr(0,7)}`;
            }
		    },
        beforeMount: function(){
            this.columns.map(function(v){
                if(v.render !== undefined){
                    v.render = new Function('data','type','row',''+v.parmas+'; return  `'+v.render+'`');
                }
            });

            var columns = this.columns;
            var rowData = this.row;
            var tableId = this.table_id;
            var ex_buttons = this.ex_buttons;
            var type = this.type;
            var vue = this;
            $(document).ready(function() {
                let domtable = $('#'+tableId+'');
                
                let dataTableConfig =
                    {
                        paging      : true,
                        ordering    : true,
                        info        : true,
                        autoWidth   : true,
		                    /* 因水平開啟會導致table 放大 跑版 故依靠jq偵測寬度開啟*/
                        scrollX: document.body.clientWidth < 813 ? true : false,
                        aaSorting : [[0, 'desc']], //預設的排序方式，第2列，升序排列
                        aLengthMenu : [25, 50, 100], //更改顯示記錄數選項
                        oLanguage: {
                            emptyTable    : "目前沒有任何（匹配的）資料。",
                            sProcessing:   "處理中...",
                            sLengthMenu:   "顯示 _MENU_ 項結果",
                            sZeroRecords:  "沒有資料",
                            sInfo:         "顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項",
                            sInfoEmpty:    "顯示第 0 至 0 項結果，共 0 項",
                            sInfoFiltered: "(從 _MAX_ 項結果過濾)",
                            sInfoPostFix:  "",
                            sSearch:       "搜索:",
                            sUrl:          "",
                            oPaginate: {
                                sFirst:    "首頁",
                                sPrevious: "上頁",
                                sNext:     "下頁",
                                sLast:     "尾頁"
                            }
                        },
                        columns: columns,
                        pageLength: vue.page_length ? vue.page_length : 25,
                        lengthChange : vue.length_change == 'hide' ? false : true
                    };
                
                if(type == 'select'){
                    
                    dataTableConfig.columnDefs= [{
                        'targets': 0,
                        'searchable':false,
                        'orderable':false,
                        'width':'1%',
                        'className': 'dt-body-center',
                        'render': function (data, type, full, meta){
                            return `<input type="checkbox" value="${full.id}">`;
                        }
                    }];
                    dataTableConfig.order = [1, 'asc'];
                    
                    // Array holding selected row IDs
                    var rows_selected = vue.$store.getters.getTableSelect;
                    rows_selected[vue.table_id] = vue.select_id ? vue.select_id : [];
		                
                    vue.$store.state.provide_total_money = vue.total_money ? vue.total_money : 0;
                    dataTableConfig.rowCallback = function(row, data, dataIndex){
                        // Get row ID
                        var rowId = data['id'];
                        
                        // If row ID is in the list of selected row IDs
                        if($.inArray(rowId, rows_selected[vue.table_id]) !== -1){
                            $(row).find('input[type="checkbox"]').prop('checked', true);
                            $(row).addClass('selected');
                        }
                    };
                }
                if(ex_buttons){
                    dataTableConfig.dom = 'Bfrtip';
                    ex_buttons.map(function(v){
                        dataTableConfig.buttons = [{
	                        extend: v ,
	                        className: 'btn btn-success btn-flat' ,
	                        text: `${v}匯出`,
	                        title:function () { return vue.getExportFileName();},
	                        filename: function () { return vue.getExportFileName();}}];
                    });
                };
                vue.dataTable = domtable.DataTable(dataTableConfig);
                vue.dataTable.clear();
                vue.dataTable.rows.add( rowData );
                vue.dataTable.draw();
                vue.getData();
                if(type == 'select'){

                    // Handle click on checkbox
                    domtable.find('tbody').on('click', 'input[type="checkbox"]', function(e){
                        var $row = $(this).closest('tr');

                        // Get row data
                        var data = vue.dataTable.row($row).data();
                        
                        // Get row ID
                        var rowId = data['id'];

                        // Determine whether row ID is in the list of selected row IDs
                        var index = $.inArray(rowId, rows_selected[vue.table_id]);
												let thisSelectMoney = $row.find("div[data-money]").data('money');
                        // If checkbox is checked and row ID is not in list of selected row IDs
                        if(this.checked && index === -1){
                            eval(`rows_selected.${vue.table_id}`).push(rowId);
                            vue.$store.state.provide_total_money += thisSelectMoney;
                            // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
                        } else if (!this.checked && index !== -1){
                            eval(`rows_selected.${vue.table_id}`).splice(index, 1);
                            vue.$store.state.provide_total_money -= thisSelectMoney;
                            
                        }

                        if(this.checked){
                            $row.addClass('selected');
                        } else {
                            $row.removeClass('selected');
                        }
                        // Prevent click event from propagating to parent
                        e.stopPropagation();
                    });

                    // Handle click on table cells with checkboxes
                    domtable.on('click', 'tbody td, thead th:first-child', function(e){
                        $(this).parent().find('input[type="checkbox"]').trigger('click');
                    });
                }
                // });
                // //chilren row
                // $('#'+tableId+' tbody').on('click', 'td.details-control', function () {
                //     var tr = $(this).closest('tr');
                //     var row = dataTable.row( tr );
                //
                //     if ( row.child.isShown() ) {
                //         // This row is already open - close it
                //         row.child.hide();
                //         tr.removeClass('shown');
                //     }
                //     else {
                //         // Open this row
                //         row.child( format(row.data()) ).show();
                //         tr.addClass('shown');
                //     }
            } );
        },
        mounted: function(){
		        
        
        },
        watch:{
            start_date: {
                immediate: true,    // 这句重要
                    handler (val, oldVal) {
                    if(oldVal !== undefined  && val !== '') {
                        this.getData();
                    }
                }
            },
            end_date: {
                immediate: true,    // 这句重要
                    handler (val, oldVal) {
                    if( oldVal !== undefined  && val !== '') {
                        this.getData();
                    }
                }
            },
            user_ids: {
                immediate: true,    // 这句重要
                    handler (val, oldVal) {
                    if(oldVal !== undefined && val !== '') {
                        this.getData();
                    }
                }
            },
            sale_group_ids: {
                immediate: true,// 这句重要
                    // lazy:true,
                    handler (val,oldVal) {
                    if( oldVal !== undefined && val !== '' ) {
                        this.getData();
                    }
                }
            }
        }
    }
</script>

<style scoped>
	#user_table >>> .point{
		cursor: pointer;
		width:100%;
	}
	#user_table >>> td, #user_table >>> th{
		text-align: center;
	}
</style>

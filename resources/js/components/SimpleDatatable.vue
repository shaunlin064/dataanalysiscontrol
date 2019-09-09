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
	
    export default {
        name: "SimpleDatatable",
        props: {
            table_id : String,
            table_head : String,
		        table_title : Array,
		        row : Array,
            columns : Array,
            ex_buttons : Array,
        },
        data() {
            return {
            }
        },
		    methods:{
              change_render(item){
                  return new Function('data','type','row', 'return `'+item+'`');
              },
		    },
        mounted: function(){
            // function format ( d ) {
            //     // `d` is the original data object for the row
            //     return  `
						// 			<table class='table table-bordered table-striped' cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">
						// 				<thead>
						// 				<tr>
						// 					<th>月份</th>
						// 					<th>媒體</th>
						// 					<th>類型</th>
						// 					<th>發放獎金</th>
						// 				</tr>
						// 				</thead>
						// 				<tbody>
						// 				${d.cue.map((item, i) =>
						// 					`<tr>
			      //               <td>${item.set_date}</td>
			      //               <td>${item.media_channel_name}</td>
			      //               <td>${item.sell_type_name}</td>
			      //               <td>${item.provide_money}</td>
		        //             </tr>`
		        //         )}
						// 				</tbody>
            //       </table>`;
            // }
            
            this.columns.map(function(v){
                if(v.render !== undefined){
                    v.render = new Function('data','type','row',''+v.parmas+'; return  `'+v.render+'`');
                }
            });
            
		        var columns = this.columns;
		        var rowData = this.row;
		        var tableId = this.table_id;
		        var ex_buttons = this.ex_buttons;
            $(document).ready(function() {
                let dataTableConfig =
                    {
                        paging      : true,
                        ordering    : true,
                        info        : true,
                        autoWidth   : true,
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
                    };
                if(ex_buttons){
                    dataTableConfig.dom = 'Bfrtip';
                    ex_buttons.map(function(v){
                        console.log(v);
                        dataTableConfig.buttons = [{ extend: v , className: 'btn btn-success btn-flat' , text: `${v}匯出`}];
                    });
                }
								
                var dataTable = $('#'+tableId+'').DataTable(dataTableConfig);
                dataTable.clear();
                dataTable.rows.add( rowData );
                dataTable.draw();
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
                // } );
            });
        },
        watch:{
            // change_date: {
            //     immediate: true,    // 这句重要
            //     handler (val, oldVal) {
            //         if(oldVal !== undefined) {
						//
            //             console.log(val,oldVal);
            //         }
            //     }
            // }
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

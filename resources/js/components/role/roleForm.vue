<template>
    <form class="form-horizontal" id='roleSettingForm'>
        <div class="box-body">
            <div class="form-group">
                <div>
                    <label class="col-sm-2 control-label pull-left">名稱</label>
                    <div class="col-sm-3">
                        <input v-if="type == 'edit'" type='text' name='name' :value='role.name' required>
                        <input v-else type='text' name='name' required>
                    </div>
                </div>
                <label class="col-sm-2 control-label pull-left">備註</label>
                
                <input v-if="type == 'edit'" type='text' name='label' :value='role.label' required>
                <input v-else type='text' name='label' required>
            </div>
            <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title pull-left">清單</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table :id='table_id' class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th></th>
                        <th>選擇</th>
                        <th>name</th>
                        <th>region</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th></th>
                        <th>選擇</th>
                        <th>name</th>
                        <th>region</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
        </div>
    </form>
</template>

<script>
    import {mapState, mapMutations, mapActions, mapGetters} from 'vuex';
    
    export default {
        name: "roleForm",
        props: {
            table_id: String,
            csrf: String,
            row: Array,
            select_id: Array,
            type:String,
            role:Object,
        },
        data() {
            return {
            }
        },
        methods: {
            format(d) {
                // `d` is the original data object for the row
                let itemTemplate = '';
                let vue = this;
                Object.keys(d.permissions).forEach(key=>{
                    itemTemplate += `<tr>
                            <td></td>
                            <td><input type='checkbox' name="permission_ids[]" class="permission" value='${d.permissions[key]['id']}'></td>
                            <td>${d.permissions[key]['label']}</td>
                            <td>${d.permissions[key]['name']}</td>
                            </tr>`;
                });

                return `<table class='table table-bordered table-striped dataTable' cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">
                            <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>label</th>
                                <th>permission.url</th>
                            </tr>
                            </thead>
                        <tbody>
                        ${itemTemplate}
                        </tbody>
                        </table>`;
            },
            checkChildAllSelect(target){
                let check = true;
                let parentDom = $(target).closest('table').closest('tr').prev('tr');
                $(target).closest('tbody').find('.permission').each(function(e,d){
                    if(d.checked === false){
                        check = false;
                    }
                });
                if(check){
                    parentDom.addClass('selected');
                    parentDom.find('input[name="permission_class_ids[]"]').prop('checked', true);
                }else{
                    parentDom.removeClass('selected');
                    parentDom.find('input[name="permission_class_ids[]"]').prop('checked', false);
                }
            }
        },
        beforeMount: function () {
            var vue = this;
            var rowData = vue.row;
            
            $(document).ready(function () {
                let domtable = $('#permissionsTable');
                let dataTableConfig =
                    {
                        paging: true,
                        ordering: true,
                        info: true,
                        autoWidth: true,
                        /* 因水平開啟會導致table 放大 跑版 故依靠jq偵測寬度開啟*/
                        scrollX: document.body.clientWidth < 813 ? true : false,
                        aaSorting: [[0, 'desc']], //預設的排序方式，第2列，升序排列
                        aLengthMenu: [25, 50, 100], //更改顯示記錄數選項
                        oLanguage: {
                            emptyTable: "目前沒有任何（匹配的）資料。",
                            sProcessing: "處理中...",
                            sLengthMenu: "顯示 _MENU_ 項結果",
                            sZeroRecords: "沒有資料",
                            sInfo: "顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項",
                            sInfoEmpty: "顯示第 0 至 0 項結果，共 0 項",
                            sInfoFiltered: "(從 _MAX_ 項結果過濾)",
                            sInfoPostFix: "",
                            sSearch: "搜索:",
                            sUrl: "",
                            oPaginate: {
                                sFirst: "首頁",
                                sPrevious: "上頁",
                                sNext: "下頁",
                                sLast: "尾頁"
                            }
                        },
                        columns: [

                            {
                                "className":'details-control',
                                "orderable":      false,
                                "data":           null,
                                "defaultContent": ''
                            },
                            {
                                data: 'id', "render": function (data, type, row, ) {
                                    return  `<p class="hidden">${data}</p><input id="checkbox${data}" name="permission_class_ids[]" type="checkbox" value=${data}>`;
                                }
                            },
                            { data: "name" },
                            { data: "region" },
                        ],
                    };
                dataTableConfig.columnDefs = [{
                    'targets': 1,
                    'searchable': false,
                    'orderable': false,
                    'width': '1%',
                    'className': 'dt-body-center',
                    'render': function (data, type, full, meta) {
                        return `<input type='checkbox' value='${full.id}'>`;
                    }
                }];
                dataTableConfig.order = [1, 'asc'];
                
                vue.dataTable = domtable.DataTable(dataTableConfig);
                vue.dataTable.clear();
                vue.dataTable.rows.add(rowData);
                vue.dataTable.draw();
                
                /*開啟所有 子選單*/
                domtable.find('tbody').find('tr').each(function(e,d){
                    var tr = $(d);
                    var row = vue.dataTable.row( tr );
                    
                    // Open all child
                    row.child( vue.format(row.data()) ).show();
                    tr.addClass('shown');
                });
                
                // Array holding selected row IDs
                var rows_selected = vue.$store.getters.getTableSelect;
                rows_selected[vue.table_id] = vue.select_id ? vue.select_id : [];
                
                //頁面載入確認已勾選項目 修改css
                $('.permission').each(function(e,d){
                    var rowId = parseInt($(d).val());
                    // If row ID is in the list of selected row IDs
                    if ($.inArray(rowId, rows_selected[vue.table_id]) !== -1) {
                        $(d).prop('checked', true);
                        $(d).closest('tr').addClass('selected');
                        vue.checkChildAllSelect(d);
                    }
                });
                
                /*父選單選取 開啟子選單與 勾選全子選單*/
                domtable.find('tbody').on('click', 'input[type="checkbox"]', function (e) {
                    var $row = $(this).closest('tr');
                    
                    /*第一層選擇後 打開子選單一併勾選*/
                    if($row.find('.details-control').parent().hasClass('shown') === false){
                        $row.find('.details-control').click();
                    }
                    
                    if (this.checked) {
                        $row.addClass('selected');
                        $(this).closest('tr').next('tr').find('table').find('input').each(function(e,d){
                            let rowId = parseInt($(this).val());
                            eval(`rows_selected.${vue.table_id}`).push(rowId);
                            $(d).prop("checked",true);
                            $(d).closest('tr').addClass('selected');
                        });
                    } else {
                        $row.removeClass('selected');
                        $(this).closest('tr').next('tr').find('table').find('input').each(function(e,d){
                            let rowId = parseInt($(this).val());
                            let index = $.inArray(rowId, rows_selected[vue.table_id]);
                            eval(`rows_selected.${vue.table_id}`).splice(index, 1);
                            $(d).prop("checked",false);
                            $(d).closest('tr').removeClass('selected');
                        });
                    }
                    
                    // Prevent click event from propagating to parent
                    e.stopPropagation();
                });

                // 子選單開闔
                $('#permissionsTable tbody').on('click', 'td.details-control', function () {

                    var tr = $(this).closest('tr');
                    var row = vue.dataTable.row( tr );
                    
                    if ( row.child.isShown() ) {
                        // This row is already open - close it
                        row.child.hide();
                        tr.removeClass('shown');
                    }
                    else {
                        // Open this row
                        row.child( vue.format(row.data()) ).show();
                        tr.addClass('shown');
                    }
                    tr.next('tr').find('table').find('input').each(function(e,d){
                        var rowId = parseInt($(d).val());
                        if ($.inArray(rowId, rows_selected[vue.table_id]) !== -1) {
                            let tr = $(d).closest('tr');
                            tr.addClass('selected');
                            tr.find('input[name="permission_ids[]"]').prop('checked', true);
                        }
                    });
                });

                // 子選單 select
                $('#permissionsTable tbody').on('click', 'input.permission', function () {
                    
                    if (this.checked) {
                            let rowId = parseInt($(this).val());
                            eval(`rows_selected.${vue.table_id}`).push(rowId);
                    } else {
                        let rowId = parseInt($(this).val());
                            let index = $.inArray(rowId, rows_selected[vue.table_id]);
                            eval(`rows_selected.${vue.table_id}`).splice(index, 1);
                    }
                    vue.checkChildAllSelect(this);
                });
            });
        }
    }
</script>

<style scoped>

</style>

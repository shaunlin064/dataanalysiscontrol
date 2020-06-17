<template>
    <form class="form-horizontal">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title pull-left" data-step="1" data-intro="權限分類表" >權限Class</h3>
                        <div class='col-md-2 pull-right'>
                            <button type="button" @click='addPost' data-step="2" data-intro="新增權限分類" class="btn btn-block btn-success">新增</button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table :id=table_id class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th data-step="3" data-intro="分類名稱 影響群組權限分類的list顯示" >名稱</th>
                                <th data-step="4" data-intro="分類歸屬 影響群組權限分類的list排列">歸屬</th>
                                <th>action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>id</th>
                                <th>名稱</th>
                                <th>歸屬</th>
                                <th>action</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
    </form>
</template>

<script>
    import {mapState, mapMutations, mapActions, mapGetters} from 'vuex';

    export default {
        name: "permissionsClassDataTable",
        props: {
            table_id: String,
            csrf: String,
            row: Array,
            ajax_url : String,
        },
        data() {
            return {}
        },
        computed: {
            ...mapState('permission', ['permission_data', 'permission_class_data']),
        },
        methods: {
            renderDatatable(){
                this.dataTable.clear();
                this.dataTable.rows.add(this.permission_class_data);
                this.dataTable.draw();
            },
            updateVuex: function (res) {
                this.$store.dispatch('permission/changePermissionClassData',res.data.permissionClassData);
            },
            axioPost(params, id){
                params['_token'] = this.csrf;
                axios({
                    url: 'permissionClassEditAjaxPost/'+id,
                    method: 'post',
                    data:params,
                    headers: {
                        'Content-Type': 'application/json',
                    }
                }).then(
                    (res)=>{
                        // 更新vux資料
                        this.updateVuex(res);
                        //更新table
                        this.renderDatatable();
                    }
                ).catch(err => console.error(err));
            },
            addPost(){
                let params = [];
                params['_token'] = this.csrf;
                axios({
                    url: 'permissionClassAddAjaxPost',
                    method: 'post',
                    data:params,
                    headers: {
                        'Content-Type': 'application/json',
                    }
                }).then(
                    (res)=>{
                        this.updateVuex(res);
                        this.renderDatatable();
                    }
                ).catch(err => console.error(err));
            },
            deletePost(id){
                let params = [];
                params['_token'] = this.csrf;
                axios({
                    url: 'permissionClassDeleteAjaxPost/'+id,
                    method: 'post',
                    data:params,
                    headers: {
                        'Content-Type': 'application/json',
                    }
                }).then(
                    (res)=>{
                        this.updateVuex(res);
                        this.renderDatatable();
                    }
                ).catch(err => console.error(err));
            },
        },
        beforeMount: function () {
            var vue = this;
            this.updateVuex({
                data: {
                    permissionClassData: vue.row
                }
            })
            $(document).ready(function () {
                let domtable = $('#' + vue.table_id);
                let dataTableConfig =
                    {
                        paging: true,
                        ordering: true,
                        info: true,
                        autoWidth: false,
                        columnDefs: [
                            { "width": "5%", "targets": 0 }
                        ],
                        /* 因水平開啟會導致table 放大 跑版 故依靠jq偵測寬度開啟*/
                        scrollX: document.body.clientWidth < 813,
                        aaSorting: [[0, 'desc']], //預設的排序方式，第2列，升序排列
                        aLengthMenu: [10, 50, 100], //更改顯示記錄數選項
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
                            {data: "id"},
                            {data: "name", "render": function (data, type, row,) {
                                    return `<input class='col-xs-12' data-id='${row.id}' name='name' value='${data}'/><div class='vis abs'>${data}</div>`;
                                }
                            },
                            {data: "region","render": function (data, type, row,) {
                                    return `<input class='col-xs-12' data-id='${row.id}' name='region' value='${data}'/><div class='vis abs'>${data}</div>`;
                                }
                            },
                            {data: "id","render": function (data, type, row,) {
                                    return `<button data-action='delete' type="button" data-id='${data}' class="btn btn-danger btn-flat">刪除</button>`;
                                }
                            },
                        ],
                    };

                vue.dataTable = domtable.DataTable(dataTableConfig);
                vue.renderDatatable();

                //監控所有 input select 做ajax post 即時更改
                domtable.on('change','input',function(){
                    let params = {
                        'val' : $(this).val(),
                        'name' : $(this).attr('name'),
                    };
                    vue.axioPost(params,$(this).data('id'));
                });

                domtable.on('click','.btn',function(e){
                    switch($(this).data('action')){
                        case 'delete':
                            if(confirm('確認刪除')){
                                vue.deletePost($(this).data('id'));
                            }
                            break;
                    }
                });
            });
        },
        watch: {
        }
    }
</script>

<style scoped>

</style>

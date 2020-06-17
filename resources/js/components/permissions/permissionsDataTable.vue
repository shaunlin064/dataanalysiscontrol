<template>
    <form class="form-horizontal">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title pull-left" data-step="5" data-intro="權限list">權限list</h3>
                    <div class='col-md-2 pull-right'>
                        <button type="button" @click='addPost' data-step="6" data-intro="權限新增"
                                class="btn btn-block btn-success">新增
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table :id=table_id class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th data-step="7" data-intro="權限分類 影響群組權限設定的分類">分類</th>
                            <th data-step="8" data-intro="描述備註">描述</th>
                            <th data-step="9" data-intro="權限影響權限 對照 route name">權限</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>id</th>
                            <th>分類</th>
                            <th>描述</th>
                            <th>權限</th>
                            <th>Action</th>
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
    import {mapState} from 'vuex';

    export default {
        name: "permissionsDataTable",
        props: {
            table_id: String,
            csrf: String,
            row: Array,
            permission_class: Array,
        },
        data() {
            return {}
        },
        computed: {
            ...mapState('permission', ['permission_data', 'permission_class_data']),
        },
        methods: {
            renderDatatable() {
                if (this.dataTable !== undefined) {
                    this.dataTable.clear();
                    this.dataTable.rows.add(this.permission_data);
                    this.dataTable.draw();
                }
            },
            renderJqPlugins() {
                $('.select2').select2();
            },
            updateVuex: function (res) {
                this.$store.dispatch('permission/changePermissionData', res.data.row);
                this.$store.dispatch('permission/changePermissionClassData', res.data.permissionClassData);
            },
            axioPost(params, id) {
                params['_token'] = this.csrf;
                axios({
                    url: 'permissionEditAjaxPost/' + id,
                    method: 'post',
                    data: params,
                    headers: {
                        'Content-Type': 'application/json',
                    }
                }).then(
                    (res) => {
                        // 更新vux資料
                        this.updateVuex(res);
                        //更新table
                        this.renderDatatable();
                        //更新 plugins
                        this.renderJqPlugins();
                    }
                ).catch(err => console.error(err));
            },
            addPost() {
                let params = [];
                params['_token'] = this.csrf;
                axios({
                    url: 'permissionAddAjaxPost',
                    method: 'post',
                    data: params,
                    headers: {
                        'Content-Type': 'application/json',
                    }
                }).then(
                    (res) => {
                        this.updateVuex(res);
                        this.renderDatatable();
                        this.renderJqPlugins();
                    }
                ).catch(err => console.error(err));
            },
            deletePost(id) {
                let params = [];
                params['_token'] = this.csrf;
                axios({
                    url: 'permissionDeleteAjaxPost/' + id,
                    method: 'post',
                    data: params,
                    headers: {
                        'Content-Type': 'application/json',
                    }
                }).then(
                    (res) => {
                        this.updateVuex(res);
                        this.renderDatatable();
                        this.renderJqPlugins();
                    }
                ).catch(err => console.error(err));
            },
        },
        beforeMount: function () {
            var vue = this;
            this.$store.dispatch('permission/changePermissionData', vue.row);

            $(document).ready(function () {
                let domtable = $('#' + vue.table_id);
                let dataTableConfig =
                    {
                        paging: true,
                        ordering: true,
                        info: true,
                        autoWidth: false,
                        columnDefs: [
                            {"width": "5%", "targets": 0}
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
                            {
                                data: "permission_class", "render": function (data, type, row,) {
                                    let template = '<div class="vis abs">' + row.permissions_class_id + '</div><select class="form-control select2" name="permissions_class_id" data-id=' + row.id + ' style="width: 100%;">';

                                    vue.permission_class_data.forEach(function (elt, idx) {
                                        if (row.permissions_class_id == elt.id) {
                                            template += `<option value='${elt.id}' selected> ${elt.name} </option>`;
                                        } else {
                                            template += `<option value='${elt.id}'> ${elt.name} </option>`;
                                        }
                                    });

                                    template += '</select>';
                                    return template;
                                }
                            },
                            {
                                data: "label", "render": function (data, type, row,) {
                                    return `<input class='col-xs-12' data-id='${row.id}' name='label' value='${data}'></input><div class='vis abs'>${data}</div>`;
                                }
                            },
                            {
                                data: "name", "render": function (data, type, row,) {
                                    return `<input class='col-xs-12' data-id='${row.id}' name='name' value='${data}'></input><div class='vis abs'>${data}</div>`;
                                }
                            },
                            {
                                data: "id", "render": function (data, type, row,) {
                                    return `<button data-action='delete' type='button' data-id='${data}' class='btn btn-danger btn-flat'>刪除</button>`;
                                }
                            },
                        ],
                    };

                vue.dataTable = domtable.DataTable(dataTableConfig);
                vue.renderDatatable();
                vue.renderJqPlugins();

                //監控所有 input select 做ajax post 即時更改
                domtable.on('change', 'input', function () {
                    let params = {
                        'val': $(this).val(),
                        'name': $(this).attr('name'),
                    };
                    vue.axioPost(params, $(this).data('id'));
                });
                domtable.on('change', '.select2', function (e) {
                    let params = {
                        'val': $(this).val(),
                        'name': $(this).attr('name'),
                    };
                    vue.axioPost(params, $(this).data('id'));
                });

                domtable.on('click', '.btn', function (e) {
                    switch ($(this).data('action')) {
                        case 'delete':
                            if (confirm('確認刪除')) {
                                vue.deletePost($(this).data('id'));
                            }
                            break;
                    }
                });
            });
        },
        mounted: function () {
        },
        watch: {
            permission_class_data: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '') {
                        this.renderDatatable();
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>

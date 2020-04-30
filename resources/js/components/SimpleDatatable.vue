<template>
    <div class="form-group">
        <div class="box box-info">
            <div class="text-center">
                <h5 class="box-title">{{table_head}}</h5>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table :id='table_id' class="table table-bordered table-striped">
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
    import {mapState} from 'vuex';

    export default {
        name: "SimpleDatatable",
        props: {
            table_id: String,
            table_head: String,
            table_title: Array,
            row: Array,
            columns: Array,
            ex_buttons: Array,
            type: String,
            csrf: String,
            ajax_url: String,
            length_change: String,
            page_length: Number,
            select_id: Array,
            total_money: Number,
            all_user_name: Object,
        },
        data() {
            return {
                now: 0,
                rows_selected: this.$store.state.table_select,
            }
        },
        computed: {
            // ...mapGetters(['getTableSelect','getUserIds','getSaleGroupIds','getStartDate','getEndDate']),
            ...mapState(['provide_money', 'loading', 'customer_profit_data', 'customer_groups_profit_data', 'medias_profit_data', 'media_companies_profit_data', 'group_progress_list', 'group_progress_list_total', 'progress_list', 'progress_list_total', 'bonus_list', 'provide_bonus_list', 'provide_groups_list', 'start_date', 'end_date', 'exchange_rates_list', 'currency', 'table_select', 'provide_statistics_list', 'provide_char_bar_stack']),
        },
        methods: {
            tableClear() {
                if (this.dataTable !== undefined) {
                    this.dataTable.clear();
                    this.dataTable.draw();
                }
            },
            updataTable(row) {

                let vue = this;
                if (vue.dataTable !== undefined) {
                    vue.dataTable.clear();
                    vue.dataTable.rows.add(row);
                    vue.dataTable.draw();
                    vue.$store.state.loading = false;
                    if ($.inArray(vue.table_id, ['provide_groups_list', 'provide_bonus_list']) !== -1) {
                        // $(`#${vue.table_id} tbody`).find('tr').each(function (e, v) {
                        //     vue.selectToggle($(v), 'select');
                        // });

                        vue.dataTable.rows().data().each(function (v, e) {
                            let thisSelectMoney = v.provide_money;
                            let groupName = v.sale_group_name ? v.sale_group_name : v.group_name;
                            let groupId = v.sale_groups_id !== undefined ? v.sale_groups_id : v.sale_groups.sale_groups_id;
                            let userName = v.user_name ? v.user_name : '';
                            vue.setStatistics(v);

                            vue.provide_statistics_list['user'][userName]['money'] += thisSelectMoney;
                            vue.provide_statistics_list['user'][userName]['group_id'] = groupId;
                            vue.provide_statistics_list['group'][groupName]['money'] += thisSelectMoney;
                            vue.provide_statistics_list['group'][groupName]['group_id'] = groupId;

                            if (vue.provide_statistics_list['user'][userName]['money'] === 0) {
                                delete vue.provide_statistics_list['user'][userName];
                            }
                            if (vue.provide_statistics_list['group'][groupName]['money'] === 0) {
                                delete vue.provide_statistics_list['group'][groupName];
                            }
                            vue.$store.state.provide_total_money += thisSelectMoney;
                        });
                    }
                }
            },
            getExportFileName() {
                return `${this.table_head}_${this.start_date.substr(0, 7)}-${this.end_date.substr(0, 7)}`;
            },
            selectToggle(dom, type) {
                let row = dom;
                let vue = this;
                // Get row data
                let data = this.dataTable.row(row).data();

                if (data !== undefined) {
                    let rowId = data.id;
                    // let rows_selected = this.$store.getters.getTableSelect;
                    let index = $.inArray(rowId, this.rows_selected[this.table_id]);
                    let thisSelectMoney = data.provide_money;
                    let groupName = data.sale_group_name ? data.sale_group_name : data.group_name;
                    let groupId = data.sale_groups_id !== undefined ? data.sale_groups_id : data.sale_groups.sale_groups_id;
                    let userName = data.user_name ? data.user_name : '';
                    this.setStatistics(data);

                    switch (type) {
                        case 'select':
                            if (index === -1) {
                                this.rows_selected[this.table_id].push(rowId);
                                this.$store.state.provide_total_money += thisSelectMoney;
                                this.provide_statistics_list['user'][userName]['money'] += thisSelectMoney;
                                this.provide_statistics_list['user'][userName]['group_id'] = groupId;
                                this.provide_statistics_list['group'][groupName]['money'] += thisSelectMoney;
                                this.provide_statistics_list['group'][groupName]['group_id'] = groupId;
                                this.provide_char_bar_stack[data.set_date][data.user_name]['provide_money'] += thisSelectMoney;
                                // eval(`this.provide_char_bar_stack.${data.set_date}.${data.user_name} += ${thisSelectMoney}`);
                            }
                            if (vue.type === 'select') {
                                $(row).find('input[type="checkbox"]').prop('checked', true);
                                $(row).addClass('selected');
                            }

                            break;
                        case 'unselect':
                            if (index !== -1) {
                                this.rows_selected[this.table_id].splice(index, 1);
                                this.$store.state.provide_total_money -= thisSelectMoney;
                                this.provide_statistics_list['user'][userName]['money'] -= thisSelectMoney;
                                this.provide_statistics_list['user'][userName]['group_id'] = groupId;
                                this.provide_statistics_list['group'][groupName]['money'] -= thisSelectMoney;
                                this.provide_statistics_list['group'][groupName]['group_id'] = groupId;
                                this.provide_char_bar_stack[data.set_date][data.user_name]['provide_money'] -= thisSelectMoney;
                                // eval(`this.provide_char_bar_stack.${data.set_date}.${data.user_name} -= ${thisSelectMoney}`);
                            }
                            if (vue.type === 'select') {
                                $(row).find('input[type="checkbox"]').prop('checked', false);
                                $(row).removeClass('selected');
                            }
                            break;
                    }

                    if (this.provide_statistics_list['user'][userName]['money'] === 0) {
                        delete this.provide_statistics_list['user'][userName];
                    }
                    if (this.provide_statistics_list['group'][groupName]['money'] === 0) {
                        delete this.provide_statistics_list['group'][groupName];
                    }

                    // if(this.$store.state.provide_char_bar_stack[data.set_date][data.user_name]['provide_money'] ===0){
                    //     delete this.$store.state.provide_char_bar_stack[data.set_date][data.user_name];
                    // }

                }

            },
            setStatistics(data) {
                /*default*/
                if (this.provide_statistics_list['user'] === undefined) {
                    this.provide_statistics_list['user'] = [];
                }
                if (this.provide_statistics_list['group'] === undefined) {
                    this.provide_statistics_list['group'] = [];
                }
                /*user*/
                if (this.provide_statistics_list['user'][data.user_name] === undefined) {
                    this.provide_statistics_list['user'][data.user_name] = [];
                    this.provide_statistics_list['user'][data.user_name]['money'] = 0;
                    this.provide_statistics_list['user'][data.user_name]['group_id'] = 0;
                }
                /*sale group*/
                let groupName = data.sale_group_name ? data.sale_group_name : data.group_name;
                if (this.provide_statistics_list['group'][groupName] === undefined) {
                    this.provide_statistics_list['group'][groupName] = [];
                    this.provide_statistics_list['group'][groupName]['money'] = 0;
                    this.provide_statistics_list['group'][groupName]['group_id'] = 0;
                }


                /*區分獎金檢視與獎金發放 */
                let separateDate = data.provide_set_date !== undefined ? data.provide_set_date : data.set_date;
                if (this.provide_char_bar_stack[separateDate] === undefined) {
                    this.provide_char_bar_stack[separateDate] = {};
                }
                if (this.all_user_name !== undefined) {
                    let allName = this.all_user_name;
                    let vue = this;
                    Object.keys(allName).forEach(k => {
                        if (vue.provide_char_bar_stack[separateDate][k] === undefined) {
                            vue.provide_char_bar_stack[separateDate][k] = {};
                            vue.provide_char_bar_stack[separateDate][k]['provide_money'] = 0;
                            vue.provide_char_bar_stack[separateDate][k]['erp_user_id'] = allName[k];
                        }
                    }, vue, separateDate);
                }
                if (this.provide_char_bar_stack[separateDate][data.user_name] === undefined) {
                    this.provide_char_bar_stack[separateDate][data.user_name] = {};
                    this.provide_char_bar_stack[separateDate][data.user_name]['provide_money'] = 0;
                }
                this.provide_char_bar_stack[separateDate][data.user_name]['erp_user_id'] = data.user !== undefined ? data.user.erp_user_id : data.sale_user.erp_user_id;
                // if(eval(`this.provide_char_bar_stack.${data.set_date} === undefined`) ){
                //     eval(`this.provide_char_bar_stack.${data.set_date} = {}`);
                //     if(eval(`this.provide_char_bar_stack.${data.set_date}.${data.user_name} === undefined`)){
                //         eval(`this.provide_char_bar_stack.${data.set_date}.${data.user_name} = 0`);
                //     }
                // }

            }
        },
        beforeMount: function () {
            this.columns.map(function (v) {
                if (v.render !== undefined) {
                    v.render = new Function('data', 'type', 'row', '' + v.parmas + '; return  `' + v.render + '`');
                }
            });

            var columns = this.columns;
            var rowData = this.row;
            var tableId = this.table_id;
            var ex_buttons = this.ex_buttons;
            var type = this.type;
            var vue = this;
            vue.rows_selected[vue.table_id] = vue.select_id ? vue.select_id : [];
            $(document).ready(function () {
                let domtable = $('#' + tableId + '');

                let dataTableConfig =
                    {
                        paging: true,
                        ordering: true,
                        info: true,
                        autoWidth: false,
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
                        columns: columns,
                        pageLength: vue.page_length ? vue.page_length : 25,
                        lengthChange: vue.length_change == 'hide' ? false : true
                    };

                if (type == 'select') {
                    dataTableConfig.order = [1, 'asc'];

                    vue.$store.state.provide_total_money = vue.total_money ? vue.total_money : 0;

                    dataTableConfig.columnDefs = [{
                        'targets': 0,
                        'searchable': false,
                        'orderable': false,
                        'width': '1%',
                        'className': 'dt-body-center',
                        'render': function (data, type, full, meta) {
                            return `<input type='checkbox' value='${full.id}'>`;
                        }
                    }];
                    /*獎金發放使用  financial provide list*/
                    if ($.inArray(tableId, ['provide_groups_list', 'provide_bonus_list']) !== -1) {
                        dataTableConfig.dom = 'Bfrtip';
                        dataTableConfig.select = true;
                        dataTableConfig.buttons = [{
                            text: 'Select',
                            className: 'btn btn-info',
                            action: function () {
                                let domtr = eval(`$('#${vue.table_id}').find('tbody tr')`);

                                domtr.each(function (e, v) {
                                    vue.selectToggle($(v), 'select');
                                });

                            }
                        },
                            {
                                text: 'Unselect',
                                className: 'btn btn-default',
                                action: function () {
                                    let domtr = eval(`$('#${vue.table_id}').find('tbody tr')`);

                                    domtr.each(function (e, v) {
                                        vue.selectToggle($(v), 'unselect');
                                    });
                                }
                            }];

                        // Handle click on checkbox
                        domtable.find('tbody').on('click', 'input[type="checkbox"]', function (e) {

                            let row = $(this).closest('tr');
                            if (this.checked) {
                                vue.selectToggle(row, 'select');
                            } else {
                                vue.selectToggle(row, 'unselect');
                            }
                            // Prevent click event from propagating to parent
                            e.stopPropagation();
                        });
                    }

                    dataTableConfig.rowCallback = function (row, data, dataIndex) {
                        // Get row ID
                        var rowId = data['id'];

                        // If row ID is in the list of selected row IDs
                        if ($.inArray(rowId, vue.rows_selected[vue.table_id]) !== -1) {

                            $(row).find('input[type="checkbox"]').prop('checked', true);
                            $(row).addClass('selected');
                        }
                    };

                    // Handle click on table cells with checkboxes
                    domtable.on('click', 'tbody td, thead th:first-child', function (e) {

                        $(this).parent().find('input[type="checkbox"]').trigger('click');
                    });
                }
                if (ex_buttons) {
                    dataTableConfig.dom = 'Bfrtip';
                    ex_buttons.map(function (v) {
                        dataTableConfig.buttons = [{
                            extend: v,
                            className: 'btn btn-success btn-flat',
                            text: `${v}匯出`,
                            title: function () {
                                return vue.getExportFileName();
                            },
                            filename: function () {
                                return vue.getExportFileName();
                            }
                        }];
                    });
                }

                vue.dataTable = domtable.DataTable(dataTableConfig);
                vue.dataTable.clear();
                vue.dataTable.rows.add(rowData);
                vue.dataTable.draw();

            });
        },
        mounted: function () {


        },
        watch: {
            customer_profit_data: {
                immediate: true,    // 这句重要
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && this.table_id == 'customer_profit_data') {

                        this.updataTable(val);
                    }
                }
            },
            customer_groups_profit_data: {
                immediate: true,    // 这句重要
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && this.table_id == 'customer_groups_profit_data') {

                        this.updataTable(val);
                    }
                }
            },
            medias_profit_data: {
                immediate: true,    // 这句重要
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && this.table_id == 'medias_profit_data') {

                        this.updataTable(val);
                    }
                }
            },
            media_companies_profit_data: {
                immediate: true,    // 这句重要
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && this.table_id == 'media_companies_profit_data') {

                        this.updataTable(val);
                    }
                }
            },
            group_progress_list: {
                immediate: true,    // 这句重要
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && this.table_id == 'group_progress_list') {

                        this.updataTable(val);
                    }
                }
            },
            group_progress_list_total: {
                immediate: true,    // 这句重要
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && this.table_id == 'group_progress_list_total') {

                        this.updataTable(val);
                    }
                }
            },
            progress_list: {
                immediate: true,    // 这句重要
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && this.table_id == 'progress_list') {

                        this.updataTable(val);
                    }
                }
            },
            progress_list_total: {
                immediate: true,    // 这句重要
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && this.table_id == 'progress_list_total') {

                        this.updataTable(val);
                    }
                }
            },
            bonus_list: {
                immediate: true,    // 这句重要
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && this.table_id == 'bonus_list') {

                        this.updataTable(val);
                    }
                }
            },
            provide_bonus_list: {
                immediate: true,    // 这句重要
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && this.table_id == 'provide_bonus_list') {

                        this.updataTable(val);
                    }
                }
            },
            provide_groups_list: {
                immediate: true,    // 这句重要
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && this.table_id == 'provide_groups_list') {

                        this.updataTable(val);
                    }
                }
            },
            exchange_rates_list: {
                immediate: true,    // 这句重要
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '' && this.table_id == 'exchange_rates_list') {
                        this.updataTable(val);
                    }
                }
            },
        }
    }
</script>

<style scoped>
    #user_table >>> .point {
        cursor: pointer;
        width: 100%;
    }

    #user_table >>> td, #user_table >>> th {
        text-align: center;
    }
</style>

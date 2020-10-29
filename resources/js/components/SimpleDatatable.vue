<template>
  <div class='form-group'>
    <div class='box box-info'>
      <div class='text-center'>
        <h5 class='box-title'>{{ table_head }}</h5>
      </div>
      <!-- /.box-header -->
      <div class='box-body'>
        <table :id='table_id' class='table table-bordered table-striped'>
          <thead>
          <tr>
            <th v-for='item in table_title'>{{ item }}</th>
          </tr>
          </thead>
          <tbody>
          </tbody>
          <tbody id='content_loader' v-if='loading'>
          <tr>
            <td colspan='14' class='text-center'>
              <i class='fa fa-spin fa-refresh' style='font-size: 3em; padding: 20px;'></i>
            </td>
          </tr>
          </tbody>
          <tfoot>
          <tr>
            <th v-for='item in table_title'>{{ item }}</th>
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
import {mapActions, mapState} from 'vuex';
import * as types from "../store/types";

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
    }
  },
  computed: {
    ...mapState('financial', ['provide_money', 'loading', 'provide_bonus_list', 'provide_groups_list', 'provide_statistics_list', 'provide_char_bar_stack', 'provide_total_money']),
    ...mapState('dateRange', ['start_date', 'end_date']),
    ...mapState('chart', ['bonus_list', 'customer_profit_data', 'customer_groups_profit_data', 'medias_profit_data', 'media_companies_profit_data', 'group_progress_list', 'group_progress_list_total', 'progress_list', 'progress_list_total', 'exchange_rates_list']),
    ...mapState('exchangeRate', ['exchange_rates_list']),
    ...mapState('dataTable', ['table_select']),
    ...mapState(['loading']),
  },
  methods: {
    ...mapActions('financial', [types.SORT_PROVIDE_STATISTICS_LIST, types.SELECT_DATA, types.SET_STATISTICS]),
    ...mapActions('dataTable', [types.PUSH_TABLE_SELECT, types.SPLICE_TABLE_SELECT]),
    tableClear() {
      if (this.dataTable !== undefined) {
        this.dataTable.clear();
        this.dataTable.draw();
      }
    },
    updataTable(row) {

      if ($('.owl-carousel').length != 0 && this.loading != false) {
        var $owl = $('.owl-carousel').owlCarousel({
          loop: false,
          center: true,
          items: 1,
          margin: 10,
          autoHeight: true,
          responsive: {
            800: {
              items: 1
            }
          }
        });

        async function sleep(ms = 0) {
          return new Promise(r => setTimeout(r, ms));
        }

        async function run() {
          await sleep(500);
          $owl.trigger('refresh.owl.carousel');
        }

        run();
      }

      let vue = this;
      if (vue.dataTable !== undefined) {
        vue.dataTable.clear();
        vue.dataTable.rows.add(row);
        vue.dataTable.draw();
        // vue.$store.state.loading = false;
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
        let index = $.inArray(rowId, this.table_select[this.table_id]);
        let table_id = this.table_id;
        this[types.SET_STATISTICS](data);

        switch (type) {
          case 'select':
            if (index === -1) {
              this[types.SELECT_DATA]({table_id, data, index, type: 'select'})
              this[types.PUSH_TABLE_SELECT]({select_id: rowId, dom_id: table_id});
            }
            if (vue.type === 'select') {
              $(row).find('input[type="checkbox"]').prop('checked', true);
              $(row).addClass('selected');
            }
            break;
          case 'unselect':
            if (index !== -1) {
              this[types.SELECT_DATA]({table_id, data, index, type: 'unselect'})
              this[types.SPLICE_TABLE_SELECT]({select_id: rowId, dom_id: table_id});
            }

            if (vue.type === 'select') {
              $(row).find('input[type="checkbox"]').prop('checked', false);
              $(row).removeClass('selected');
            }
            break;
        }

        this[types.SORT_PROVIDE_STATISTICS_LIST];
      }

    },
  },
  beforeMount: function () {
    this.columns.map(function (v) {
      if (v.render !== undefined) {
        v.render = new Function('data', 'type', 'row', '' + v.parmas + '; return  `' + v.render + '`');
      }
    });
    //
    var columns = this.columns;
    var rowData = this.row;
    var tableId = this.table_id;
    var ex_buttons = this.ex_buttons;
    var type = this.type;
    var vue = this;
    vue.table_select[vue.table_id] = vue.select_id ? vue.select_id : [];
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
          if ($.inArray(rowId, vue.table_select[vue.table_id]) !== -1) {
            $(row).find('input[type="checkbox"]').prop('checked', true);
            $(row).addClass('selected');
          }
        };

        // Handle click on table cells with checkboxes
        domtable.on('click', 'tbody td, thead th:first-child', function (e) {

          $(this).parent().find('input[type="checkbox"]').trigger('click');
          let targetId = $(this).parent().find('input[type="checkbox"]').attr('id');

          if ($('#' + targetId).is(':checked')) {
            $('#' + targetId).parent().parent().addClass('selected');
          } else {
            $('#' + targetId).parent().parent().removeClass('selected');
          }
        });
      }

      /*業績檢視 客戶列表*/
      if ($.inArray(tableId, ['customer_profit_data']) !== -1) {
        domtable.on('click', 'tr td a.customer_chart_link', function (e, v) {
          bus.$emit('getCustomerChartData', {type: $(this).data('type'), id: $(this).data('id')});

        })
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
      immediate: false,
      handler(val, oldVal) {
        if (oldVal !== undefined && val !== '' && this.table_id == 'customer_profit_data') {

          this.updataTable(val);
        }
      }
    },
    customer_groups_profit_data: {
      immediate: false,
      handler(val, oldVal) {
        if (oldVal !== undefined && val !== '' && this.table_id == 'customer_groups_profit_data') {
          this.updataTable(val);
        }
      }
    },
    medias_profit_data: {
      immediate: false,
      handler(val, oldVal) {
        if (oldVal !== undefined && val !== '' && this.table_id == 'medias_profit_data') {

          this.updataTable(val);
        }
      }
    },
    media_companies_profit_data: {
      immediate: false,
      handler(val, oldVal) {
        if (oldVal !== undefined && val !== '' && this.table_id == 'media_companies_profit_data') {

          this.updataTable(val);
        }
      }
    },
    group_progress_list: {
      immediate: false,
      handler(val, oldVal) {
        if (oldVal !== undefined && val !== '' && this.table_id == 'group_progress_list') {

          this.updataTable(val);
        }
      }
    },
    group_progress_list_total: {
      immediate: false,
      handler(val, oldVal) {
        if (oldVal !== undefined && val !== '' && this.table_id == 'group_progress_list_total') {

          this.updataTable(val);
        }
      }
    },
    progress_list: {
      immediate: false,
      handler(val, oldVal) {
        if (oldVal !== undefined && val !== '' && this.table_id == 'progress_list') {

          this.updataTable(val);
        }
      }
    },
    progress_list_total: {
      immediate: false,
      handler(val, oldVal) {
        if (oldVal !== undefined && val !== '' && this.table_id == 'progress_list_total') {

          this.updataTable(val);
        }
      }
    },
    bonus_list: {
      immediate: false,
      handler(val, oldVal) {
        if (oldVal !== undefined && val !== '' && this.table_id == 'bonus_list') {

          this.updataTable(val);
        }
      }
    },
    provide_bonus_list: {
      immediate: false,
      handler(val, oldVal) {
        if (oldVal !== undefined && val !== '' && this.table_id == 'provide_bonus_list') {

          this.updataTable(val);
        }
      }
    },
    provide_groups_list: {
      immediate: false,
      handler(val, oldVal) {
        if (oldVal !== undefined && val !== '' && this.table_id == 'provide_groups_list') {

          this.updataTable(val);
        }
      }
    },
    exchange_rates_list: {
      immediate: false,
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

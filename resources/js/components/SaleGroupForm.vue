<template>
  <div class='active tab-pane' id='settings'>
    <form class='form-horizontal' :action='this.arg.form_action' method='post' id='bonusSettingForm'>
      <input type='hidden' id='user_now_select' name='user_now_select' v-model='user_now_select'>
      <input type='hidden' id='user_now_select_is_convener' name='user_now_select_is_convener'
             v-model='user_now_select_is_convener'>

      <div class='box-body'>
        <div class='form-group'>
          <input type='hidden' name='_token' :value=csrf_token>
        </div>
        <div class='box box-widget widget-user'>
          <!-- Add the bg color to the header using any of the bg-* classes -->
          <div class='widget-user-header bg-aqua-active'>
            <div class='col-sm-4 border-right'>
              <div class='description-block'>
                <h5 class='' data-step='2' data-intro='召集人獎金比例建議 最大5.5% 下修每增加1人 -0.25%'>獎金比例</h5>
                <input type='text' name='rate' class='form-control text-center' v-model='bonus_rate'
                       @input='checkInput'>
                <div class='alert alert-danger alert-dismissible' v-if='bonus_rate > max_rate'>
                  獎金比例不能超過{{ max_rate }}%
                </div>

              </div>
              <!-- /.description-block -->
            </div>
            <!-- /.col -->
            <div class='col-sm-4 border-right'>
              <div class='description-block'>
                <h5 class=''>團隊名稱</h5>
                <div class='description-text' :class="{'has-error' : has_error}">
                  <input type='text' name='name' class='form-control text-center' id='groupName' placeholder='請輸入團隊名稱'
                         v-model='name' data-step='3' data-intro='團隊名稱'>
                  <span class='help-block' :class="{'hidden' : !has_error }">團隊名稱 不能為空</span>
                </div>
                <input type='hidden' name='sale_group_id' :value='this.sale_group_id'>
              </div>
              <!-- /.description-block -->
            </div>
            <!-- /.col -->
            <div class='col-sm-4'>
              <div class='description-block'>
                <h5 class=''>責任額總計</h5>
                <h3 class='description-text' id='total_boundary' data-step='4' data-intro='責任額總計為團隊內所有人當月責任額'>
                  {{ total_boundary | money }}</h3>
              </div>
              <!-- /.description-block -->
            </div>
            <!-- /.col -->
          </div>
        </div>
        <div class='form-group'>
          <div class='box box-info'>
            <div class='text-center'>
              <h5 class='box-title'>英雄榜</h5>
            </div>
            <div class='box-body'>
              <table class='table table-bordered table-striped'>
                <thead class='thead-light'>
                <tr>
                  <th width='1'>#</th>
                  <th width='25%' data-step='5' data-intro='團隊毛利達成比例'>達成比例</th>
                  <!--<th width="25%">獎金比例</th>-->
                  <th width='25%' data-step='6' data-intro='額外獎金為召集人的英雄榜獎金'>額外獎金</th>
                  <th width='25%' data-step='7' data-intro='編輯須按下儲存'>Action</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for='(item, index) in items' :key='index'>
                  <td>{{ index + 1 }}</td>
                  <td :class="{'has-error' : detail_has_error[index]['achieving_rate']}">
                    <span v-if='editIndex !== index'>{{ item.achieving_rate }}</span>
                    <input type='number' class='form-control form-control-sm' v-model='item.achieving_rate'
                           v-else-if='editIndex === index'>
                    <span class='help-block'
                          :class="{'hidden' : !detail_has_error[index]['achieving_rate'] }">達成比例不能為0</span>
                  </td>
                  <td :class="{'has-error' : detail_has_error[index]['bonus_direct']}">
                    <span v-if='editIndex !== index'>{{ item.bonus_direct }}</span>
                    <span v-if='editIndex === index'>
				              <input type='number' class='form-control form-control-sm' v-model='item.bonus_direct'>
				            </span>
                  </td>
                  <td>
				            <span v-if='editIndex !== index'>
						            <div v-for='(item, index ) in items'>
						              <input type='hidden' :name='"groupsBonus["+index+"][achieving_rate]"'
                                 :value='item.achieving_rate'>
							            <input type='hidden' :name='"groupsBonus["+index+"][bonus_rate]"' :value='item.bonus_rate'>
							            <input type='hidden' :name='"groupsBonus["+index+"][bonus_direct]"'
                                 :value='item.bonus_direct'>
						            </div>
					              <button type='button' @click='edit(item, index)' class='btn btn-info'>編輯</button>
					              <button type='button' @click='remove(item, index)' class='btn btn-danger'>刪除</button>
				            </span>
                    <span v-else>
											<button type='button' class='btn btn-info' @click='save()'>儲存</button>
				              <button type='button' class='btn btn-danger' @click='cancel(item)'>取消</button>
				            </span>
                  </td>
                </tr>
                </tbody>
              </table>

              <div class='col-3 offset-9 text-right my-3'>
                <button type='button' @click='add' class='btn btn-success'>新增</button>
              </div>

            </div>
          </div>
        </div>
        <div class='form-group' v-if='bonus_beyond_setting_sale_group_ids.includes(parseInt(this.sale_group_id))'>
          <div class='box box-info'>
            <div class='text-center'>
              <h5 class='box-title'>領導獎金</h5>
            </div>
            <div class='box-body'>
              <table class='table table-bordered table-striped'>
                <thead class='thead-light'>
                <tr>
                  <th width='1'>#</th>
                  <th width='25%'>達成比例</th>
                  <th width='25%'>額外獎金</th>
                  <th width='25%'>Action</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for='(item, index) in bonus_beyond_role["level"]' :key='index'>
                  <td>{{ index + 1 }}</td>
                  <td>{{ item.rate }}</td>
                  <td>{{ item.bonus_direct }}</td>
                  <td></td>
                </tr>
                <tr :key='4'>
                  <td>4</td>
                  <td>全員包含招集人100%</td>
                  <td>{{ bonus_beyond_role.extra_bonus }}</td>
                  <td></td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <simple-data-table-componet
            :table_id='"user_table"'
            :table_head='"人員清單"'
            :table_title='["選擇","招集人","名稱","責任額","部門"]'
            :row=JSON.parse(this.arg.userdata)
            :columns='columns'
            data-step='9' data-intro='團隊人員選擇 一個團隊只能有一個召集人 且召集人也需要被選入團隊'></simple-data-table-componet>
        <button type='button' @click='submit' class='btn btn-info pull-right' data-step='8' data-intro='最後送出才會完成修改'>送出
        </button>
      </div>
      <!-- /.box-body -->

      <!-- /.box-footer -->
    </form>
  </div>
</template>

<script>
export default {
  props: {
    arg: Object,
  },
  data() {
    return {
      columns: [
        {
          data: "groups_users",
          render: '<p class="hidden">${data}</p><input id="checkbox${row.erp_user_id}" class="groupsUsers" type="checkbox" value=${row.erp_user_id} ${checkt}>',
          parmas: 'let checkt = data == 1 ? "checked" : "" '
        },
        {
          data: "groups_is_convener",
          render: '<p class="hidden">${data}</p><input class="is_convener" type="checkbox" value=${row.erp_user_id} ${checkt}>',
          parmas: 'let checkt = data == 1 ? "checked" : "" '
        },
        {data: "name", render: '<label class="point" for=checkbox${row.erp_user_id}>${data}</label>'},
        {
          data: "boundary",
          render: '<label class="point" data-boundary_id=${row.erp_user_id} data-boundary=${data} for=checkbox${row.erp_user_id}>${data}</label>'
        },
        {data: "sale_groups_name", render: '<label class="point" for=checkbox${row.erp_user_id}>${data}</label>'}
      ],
      csrf_token: this.arg.csrf_token,
      sale_group_id: this.arg.sale_group_id ? this.arg.sale_group_id : 0,
      items: this.arg.bonus ? JSON.parse(this.arg.bonus) : [],
      detail_has_error: this.arg.bonus ? JSON.parse(this.arg.bonus).map(function (v, k) {
        return {achieving_rate: false, bonus_rate: false};
      }) : [],
      has_error: false,
      name: this.arg.name ? this.arg.name : '',
      groups_users: this.arg.groups_users ? JSON.parse(this.arg.groups_users) : [],
      originalData: null,
      editIndex: null,
      user_now_select: this.arg.user_now_select ? JSON.parse(this.arg.user_now_select) : [],
      user_now_select_is_convener: this.arg.user_now_select_is_convener ? JSON.parse(this.arg.user_now_select_is_convener) : [],
      total_boundary: this.arg.total_boundary ? parseInt(this.arg.total_boundary) : 0,
      max_rate: this.arg.max_rate,
      bonus_rate: this.arg.bonus_rate,
      bonus_beyond_role: this.arg.bonus_beyond_role ? JSON.parse(this.arg.bonus_beyond_role) : [],
      bonus_beyond_setting_sale_group_ids : this.arg.bonus_beyond_setting_sale_group_ids ? JSON.parse(this.arg.bonus_beyond_setting_sale_group_ids) : [],
      count_rate: 0,
    }
  },
  filters: {
    money: (value) => new Intl.NumberFormat('ja-JP').format(value),
    percentage: (value) => `${value}%`,
  },
  computed: {},
  methods: {
    checkInput() {
      this.bonus_rate = this.dealInputVal(this.bonus_rate);
    },
    dealInputVal(value) {
      value = value.replace(/^0*(0\.|[1-9])/, "$1");
      value = value.replace(/[^\d.]/g, ""); //清除"数字"和"."以外的字符
      value = value.replace(/^\./g, ""); //验证第一个字符是数字而不是字符
      value = value.replace(/\.{1,}/g, "."); //只保留第一个.清除多余的
      value = value
          .replace(".", "$#$")
          .replace(/\./g, "")
          .replace("$#$", ".");
      value = value.replace(/^(\-)*(\d*)\.(\d\d).*$/, "$1$2.$3"); //只能输入两个小数
      value = value.indexOf(".") > 0
          ? value.split(".")[0].substring(0, 10) + "." + value.split(".")[1]
          : value.substring(0, 10);

      return value;
    },
    add() {
      this.originalData = null;
      this.items.push({achieving_rate: '0', bonus_rate: '0', bonus_direct: '0'});
      this.detail_has_error.push({achieving_rate: false, bonus_rate: false});

      this.editIndex = this.items.length - 1;
      this.detail_has_error[this.editIndex] = {achieving_rate: false, bonus_rate: false};
    },

    edit(item, index) {
      this.originalData = Object.assign({}, item);
      this.editIndex = index;
    },

    cancel(item) {
      this.editIndex = null;
      if (!this.originalData) {
        this.items.splice(this.items.indexOf(item), 1);
        return;
      }
      ;

      Object.assign(item, this.originalData);
      this.originalData = null;
    },

    remove(item, index) {
      this.items.splice(index, 1);
      this.$set(this.items, 'penguin', 0);
      return;
    },

    save() {
      this.has_error = true;
      this.has_error = false;
      this.detail_check();

      if (!this.detail_error_check) {
        this.originalData = null;
        this.editIndex = null;
      }
      ;
    },

    submit() {
      if (this.name == '') {
        this.has_error = true;
      } else {
        this.has_error = false;
      }

      this.detail_check();
      if (!(this.has_error) && !(this.detail_error_check)) {
        $('#bonusSettingForm').submit();
      }
    },
    detail_check() {
      if (this.name == '') {
        $('#groupName').focus();
        return;
      }

      if (this.user_now_select_is_convener.length > 1) {
        this.has_error = true;
        alert('招集人不能超過一位');
        return;
      }

      if (this.bonus_rate > this.max_rate) {
        alert('獎金比例不能超過5.5');
        return;
      }

      let detail_has_error = this.detail_has_error;
      const values = Object.values(this.items);
      let detail_error_check = false;
      values.map(function (v, k) {
        detail_has_error[k]['achieving_rate'] = v.achieving_rate == 0 ? true : false;
        if (v.achieving_rate == 0) {
          detail_error_check = true;
        }
      });
      this.detail_has_error = detail_has_error;
      this.detail_error_check = detail_error_check;

      return this.detail_error_check;
    },
    check_is_set(id) {
      return (this.alreadySetUserIds.indexOf(id * 1) < 0) ? null : true;
    }
  },
  mounted: function () {

    var evTimeStamp = 0;
    var vue = this;

    $('.row').on('click', 'input[type="checkbox"]', function (v, k) {
      var now = +new Date();
      if (now - evTimeStamp < 100) {
        return;
      }
      evTimeStamp = now;

      let checkStatus = $(this).prop("checked");
      let thisValue = parseInt($(this).val());

      if ($(this).hasClass('is_convener')) {
        if (checkStatus) {
          vue.user_now_select_is_convener.push(thisValue);
        } else {
          vue.user_now_select_is_convener = vue.user_now_select_is_convener.filter(e => e !== thisValue);
        }
      } else if ($(this).hasClass('groupsUsers')) {
        let boundary = $('*[data-boundary_id="' + thisValue + '"]').data('boundary');
        if (checkStatus) {
          vue.total_boundary += $(this).parent().parent().find('[data-boundary]').data('boundary');
          vue.user_now_select.push(thisValue);

          if (boundary !== undefined && boundary !== 0) {
            vue.count_rate++;
          }
        } else {
          vue.user_now_select = vue.user_now_select.filter(e => e !== thisValue);
          vue.total_boundary -= $(this).parent().parent().find('[data-boundary]').data('boundary');
          if (boundary !== undefined && boundary !== 0) {
            vue.count_rate--;
          }
        }
      }
    });
  },
}
</script>

<style>
input[type="number"] {
  text-align: right;
}

.has-error span.help-block {
  color: #dd4b39;
}

.has-error input[type="number"] {
  border-color: #dd4b39;
}
</style>

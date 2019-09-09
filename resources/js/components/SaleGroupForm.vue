<template>
	<div class="active tab-pane" id="settings">
		<!-- Horizontal Form -->
		<div class="box box-info">
			<div class="box-header with-border">
				<h3 class="box-title">{{arg.date}}</h3>
			</div>
			<!-- /.box-header -->
			<!-- form start -->
			<form class="form-horizontal" :action='this.arg.form_action' method='post' id='bonusSettingForm'>
				<input type='hidden'  id='user_now_select' name='user_now_select' v-model='user_now_select'>
				<input type='hidden'  id='user_now_select_is_convener' name='user_now_select_is_convener' v-model='user_now_select_is_convener'>
				<div class="box-body">
					<div class="form-group">
						<input type='hidden' name="_token" :value=csrf_token>
					</div>
					<div class="form-group">
						<!--<div>-->
						<!--	<label for="selectAddUserId" class="col-sm-2 control-label pull-left">新增業務</label>-->
						<!--	<div class="col-sm-3">-->
						<!--		<select class="form-control select2" style="width: 100%;" name='erp_user_id'>-->
						<!--			<optgroup >-->
						<!--				<option></option>-->
						<!--			</optgroup>-->
						<!--		</select>-->
						<!--	</div>-->
						<!--</div>-->
						<input type='hidden' name='sale_group_id' :value='this.sale_group_id'>
						<label for="groupName" class="col-sm-2 control-label pull-left">團隊名稱</label>
						<div class="col-sm-3" :class="{'has-error' : has_error}">
							<input type='text' name='name' class="form-control" id="groupName" placeholder="請輸入團隊名稱" v-model='name'>
							<span class="help-block" :class="{'hidden' : !has_error }">團隊名稱 不能為空</span>
						</div>
					</div>
					<div class='form-group'>
						<div class="box-body">
							<table class="table table-bordered table-striped">
								<thead class="thead-light">
								<tr>
									<th width="1">#</th>
									<th width="25%">達成金額</th>
									<th width="25%">獎金比例</th>
									<!--<th width="25%">額外獎金</th>-->
									<th width="25%">Action</th>
								</tr>
								</thead>
								<tbody>
								<tr v-for="(item, index) in items" :key="index">
									<td>{{ index + 1 }}</td>
									<td :class="{'has-error' : detail_has_error[index]['achieving_money']}">
										<span v-if="editIndex !== index">{{ item.achieving_money }}</span>
										<input type='number' class="form-control form-control-sm" v-model="item.achieving_money" v-else-if="editIndex === index">
										<span class="help-block" :class="{'hidden' : !detail_has_error[index]['achieving_money'] }">達成比例不能為0</span>
									</td>
									<td :class="{'has-error' : detail_has_error[index]['bonus_rate']}">
										<span v-if="editIndex !== index">{{ item.bonus_rate }}</span>
										<input type='number' class="form-control form-control-sm" v-model="item.bonus_rate" v-else-if="editIndex === index">
										<span class="help-block" :class="{'hidden' : !detail_has_error[index]['bonus_rate'] }">獎金比例不能為0</span>
									</td>
									<!--<td :class="{'has-error' : detail_has_error[index]['bonus_direct']}">-->
									<!--	<span v-if="editIndex !== index">{{ item.bonus_direct }}</span>-->
									<!--	<input type='number' class="form-control form-control-sm" v-model="item.bonus_direct" v-else-if="editIndex === index">-->
									<!--</td>-->
									<td>
				            <span v-if="editIndex !== index">
						            <div v-for='(item, index ) in items'>
						              <input type='hidden' :name='"groupsBonus["+index+"][achieving_money]"' :value='item.achieving_money'>
							            <input type='hidden' :name='"groupsBonus["+index+"][bonus_rate]"' :value='item.bonus_rate'>
							            <input type='hidden' :name='"groupsBonus["+index+"][bonus_direct]"' :value='item.bonus_direct'>
						            </div>
					              <button type="button" @click="edit(item, index)" class="btn btn-info">編輯</button>
					              <button type="button" @click="remove(item, index)" class="btn btn-danger">刪除</button>
				            </span>
										<span v-else>
											<button type="button" class="btn btn-info" @click="save()">儲存</button>
				              <button type="button" class="btn btn-danger" @click="cancel(item)">取消</button>
				            </span>
									</td>
								</tr>
								</tbody>
							</table>
							
							<div class="col-3 offset-9 text-right my-3">
								<button type='button' @click="add" class="btn btn-success">新增</button>
							</div>
						
						</div>
					</div>
					<simple-data-table-componet
					 :table_id='"user_table"'
					 :table_head='"人員清單"'
					 :table_title='["選擇","招集人","名稱","部門"]'
					 :row = JSON.parse(this.arg.userdata)
					 :columns = 'columns'
					></simple-data-table-componet>
				</div>
				<!-- /.box-body -->
				<div class="box-footer">
					<button type="button" @click='submit' class="btn btn-info pull-right">送出</button>
				</div>
				<!-- /.box-footer -->
			</form>
		</div>
	</div>
</template>

<script>
    export default {
        props: {
            arg : Object,
        },
        data() {
            return {
                columns : [
                    {data: "groups_users", render: '<p class="hidden">${data}</p><input id="checkbox${row.id}" class="groupsUsers" type="checkbox" value=${row.id} ${checkt}>',parmas:'let checkt = data == 1 ? "checked" : "" '},
                    {data: "groups_is_convener", render: '<p class="hidden">${data}</p><input class="is_convener" type="checkbox" value=${row.id} ${checkt}>',parmas:'let checkt = data == 1 ? "checked" : "" '},
                    {data: "name", render: '<label class="point" for=checkbox${row.id}>${data}</label>'},
                    {data: "department_name", render: '<label class="point" for=checkbox${row.id}>${data}</label>'}
                ],
                csrf_token : this.arg.csrf_token,
                sale_group_id: this.arg.sale_group_id ? this.arg.sale_group_id : 0,
                items:  this.arg.bonus ? JSON.parse(this.arg.bonus) :[],
                detail_has_error: this.arg.bonus ? JSON.parse(this.arg.bonus).map(function(v,k){
                    return {achieving_rate: false,bonus_rate:false};
                }) : [],
                has_error: false,
                name : this.arg.name ? this.arg.name : '',
                groups_users: this.arg.groups_users ? JSON.parse(this.arg.groups_users) : [],
                originalData: null,
                editIndex: null,
                user_now_select: this.arg.user_now_select ? JSON.parse(this.arg.user_now_select) : [],
                user_now_select_is_convener: this.arg.user_now_select_is_convener ? JSON.parse(this.arg.user_now_select_is_convener) : [],
            }
        },
        methods: {

            add() {
                this.originalData = null;
                this.items.push({ achieving_money: '0', bonus_rate: '0', bonus_direct: '0'});
                this.detail_has_error.push({achieving_money: false, bonus_rate: false});

                this.editIndex = this.items.length - 1;
                this.detail_has_error[this.editIndex]= {achieving_money: false, bonus_rate: false};
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
                };

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
                
                if(!this.detail_error_check){
                    this.originalData = null;
                    this.editIndex = null;
                };
            },

            submit(){
                if(this.name == ''){
                    this.has_error = true;
                }else{
                    this.has_error = false;
                }
						
                if(this.user_now_select.length == 0){
                    alert('請選擇團隊成員');
                    return;
                }
                if(Object.keys(this.items).length == 0){
                    alert('請設定獎金級距');
                    return;
                }
                this.detail_check();
								
                if( !(this.has_error) && !(this.detail_error_check) ){
                    $('#bonusSettingForm').submit();
                }
            },
            detail_check(){
                let detail_has_error = this.detail_has_error;
                const values = Object.values(this.items);
                let detail_error_check = false;
                values.map(function(v,k){
                    detail_has_error[k]['achieving_money'] = v.achieving_money == 0 ? true : false;
                    detail_has_error[k]['bonus_rate'] = v.bonus_rate == 0 ? true : false;
                    if(v.achieving_money == 0 || v.bonus_rate == 0){
                        detail_error_check = true;
                    }
                });
                this.detail_has_error = detail_has_error;
                this.detail_error_check = detail_error_check;

                return this.detail_error_check;
            },
            check_is_set(id){
                return (this.alreadySetUserIds.indexOf(id*1) < 0) ? null : true;
            }
        },
        mounted: function() {
            // $('.select2').select2();
		        // for( let key in this.bonus){
            //     console.log(key,this.bonus[key]);
		        // }
            var evTimeStamp = 0;
            var vue = this;
		        $('.row').on('click','input[type="checkbox"]',function(v,k){
                var now = +new Date();
                if (now - evTimeStamp < 100) {
                    return;
                }
                evTimeStamp = now;
                
                let checkStatus = $(this).prop("checked");
                let thisValue = parseInt($(this).val());
                
				        if($(this).hasClass('is_convener')){
                    if(checkStatus){
                        vue.user_now_select_is_convener.push(thisValue);
                    }else{
                        vue.user_now_select_is_convener = vue.user_now_select_is_convener.filter(e => e !== thisValue);
                    }
				        }else if($(this).hasClass('groupsUsers')){
                    if(checkStatus){
                        vue.user_now_select.push(thisValue);
                    }else{
                        vue.user_now_select = vue.user_now_select.filter(e => e !== thisValue);
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
	.has-error span.help-block{
		color:#dd4b39;
	}
	.has-error input[type="number"]{
		border-color: #dd4b39;
	}
</style>

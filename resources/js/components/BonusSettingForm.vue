<template>
	<div class="active tab-pane" id="settings">
		<!-- Horizontal Form -->
		<div class="box box-info">
			<div class="box-header with-border">
				<h3 class="box-title">當月獎金設定值</h3>
			</div>
			<!-- /.box-header -->
			<!-- form start -->
			<form class="form-horizontal" :action='form_action' method='post' id='bonusSettingForm'>
				<div class="box-body">
					<div class="form-group">
						<input type='hidden' name="_token" :value='csrf'>
					</div>
					<div class="form-group">
						<div v-if='type == "add"'>
							<label for="selectAddUserId" class="col-sm-2 control-label pull-left">新增業務</label>
							<div class="col-sm-3">
								<select class="form-control select2" style="width: 100%;" name='erp_user_id'>
									<optgroup v-for='user_groups in addUserList' :label='user_groups["depName"]'>
										<option v-for='add_user_list in user_groups.data' :disabled='check_is_set(add_user_list.id)' :value='add_user_list.id'>{{ add_user_list.name }}</option>
									</optgroup>
								</select>
							</div>
						</div>
						<input type='hidden' name='bonus_id' :value='this.row.id' v-if='type == "edit"'>
						<label for="inputBoundary" class="col-sm-2 control-label pull-left">責任額</label>
						<div class="col-sm-3" :class="{'has-error' : has_error}" data-step="2" data-intro="責任額">
							<input type='number' name='boundary' class="form-control" id="inputBoundary" placeholder="個人責任額" v-model='boundary' >
							<span class="help-block" :class="{'hidden' : !has_error }">個人責任額 不能為0</span>
						</div>
					</div>
					<div class='form-group'>
						<div class="box-body" data-step="3" data-intro="責任額級距">
							<table class="table table-bordered table-striped">
								<thead class="thead-light">
								<tr>
									<th width="1">#</th>
									<th width="25%" data-step="3" data-intro="毛利達成責任額百分比">達成比例</th>
									<th width="25%" data-step="4" data-intro="獎金比例 為 毛利乘上該百分比">獎金比例</th>
									<th width="25%" data-step="5" data-intro="額外獎金 為英雄榜使用 此欄為直接加於金額">額外獎金</th>
									<th width="25%" data-step="6" data-intro="編輯須按下儲存">Action</th>
								</tr>
								</thead>
								<tbody>
								<tr v-for="(item, index) in items" :key="index">
									<td>{{ index + 1 }}</td>
									<td :class="{'has-error' : detail_has_error[index]['achieving_rate']}">
										<span v-if="editIndex !== index">{{ item.achieving_rate }}</span>
										<span v-if="editIndex === index">
				                            <input type='number' class="form-control form-control-sm" v-model="item.achieving_rate">
				                        </span>
										<span class="help-block" :class="{'hidden' : !detail_has_error[index]['achieving_rate'] }">達成比例不能為0</span>
									</td>
									<td :class="{'has-error' : detail_has_error[index]['bonus_rate']}">
										<span v-if="editIndex !== index">{{ item.bonus_rate }}</span>
                                        <span v-if="editIndex === index">
                                          <input type='number' class="form-control form-control-sm" v-model="item.bonus_rate">
                                        </span>
										<span class="help-block" :class="{'hidden' : !detail_has_error[index]['bonus_rate'] }">獎金比例不能為0</span>
									</td>
									<td :class="{'has-error' : detail_has_error[index]['bonus_direct']}">
										<span v-if="editIndex !== index">{{ item.bonus_direct }}</span>
										<span v-if="editIndex === index">
                                          <input type='number' class="form-control form-control-sm" v-model="item.bonus_direct">
                                        </span>
									</td>
									<td>
				            <span v-if="editIndex !== index">
						            <div v-for='(item, index ) in items'>
						              <input type='hidden' :name='"bonus_levels["+index+"][achieving_rate]"' :value='item.achieving_rate'>
							            <input type='hidden' :name='"bonus_levels["+index+"][bonus_rate]"' :value='item.bonus_rate'>
							            <input type='hidden' :name='"bonus_levels["+index+"][bonus_direct]"' :value='item.bonus_direct'>
						            </div>
					              <button type="button" @click="edit(item, index)" class="btn btn-info">編輯</button>
					              <button type="button" @click="remove(item, index)" class="btn btn-danger">刪除</button>
				            </span>
                            <span v-else>
											<button type="button" class="btn btn-info" @click="save(item)">儲存</button>
				              <button type="button" class="btn btn-danger" @click="cancel(item)">取消</button>
				            </span>
									</td>
								</tr>
								</tbody>
							</table>
							
							<div class="col-3 offset-9 text-right my-3" >
								<button type='button' v-show="!editIndex" @click="add" class="btn btn-success">新增</button>
							</div>
						
						</div>
					</div>
				
				</div>
				<!-- /.box-body -->
				<div class="box-footer" >
					<button type="button" @click='submit()' class="btn btn-info pull-right" data-step="7" data-intro="最後送出才會完成修改">送出</button>
				</div>
				<!-- /.box-footer -->
			</form>
		</div>
	</div>
</template>

<script>
    export default {
        props: {
            row : Object,
            type: String,
            add_user_lists: Array,
            csrf_token : String,
            form_action : String,
            already_set_user_ids: Array,
        },
        filters: {
            money: (value) => new Intl.NumberFormat('ja-JP', { style: 'currency', currency: 'TWD',minimumFractionDigits: 0 }).format(value)
        },

        data() {
            return {
                has_error: false,
                editIndex: null,
                detail_has_error: this.row.levels ? this.row.levels.map(function(v,k){
                    return {achieving_rate: false,bonus_rate:false};
                }) : {},
                detail_error_check: false,
                originalData: null,
                items: this.row.levels ? this.row.levels : [],
                boundary: this.row.boundary ? this.row.boundary : 0,
                addUserList : this.add_user_lists,
                csrf: this.csrf_token,
                alreadySetUserIds : this.already_set_user_ids,
            }
        },
        mounted: function() {
            // console.log(this.check_is_set(1));
						$('.select2').select2();
						// console.log(this.items);
				},
		    methods: {
		            add() {
		                // console.log(this.items);
		                this.originalData = null;
		                this.items.push({ achieving_rate: '0', bonus_rate: '0', bonus_direct: '0'});
                    // this.detail_has_error.push({achieving_rate: false, bonus_rate: false});
                    
		                this.editIndex = this.items.length - 1;
                    this.detail_has_error[this.editIndex]= {achieving_rate: false, bonus_rate: false};
		            },
		
		            edit(item, index) {
		                this.originalData = Object.assign({}, item)
		                this.editIndex = index
		            },
		
		            cancel(item) {
		                this.editIndex = null
		
		                if (!this.originalData) {
		                    this.items.splice(this.items.indexOf(item), 1)
		                    return
		                }
		
		                Object.assign(item, this.originalData)
		                this.originalData = null
		            },
		
		            remove(item, index) {
		                this.items.splice(index, 1);
		                this.$set(this.items, 'penguin', 0);
				            // console.log(this.originalData);
		                return
		            },
		
		            save(item) {
                    // this.detail_error_check =
                    this.has_error = true;
                    this.has_error = false;
                    this.detail_check();
                    // console.log(this.detail_error_check);
                    if(!this.detail_error_check){
                        this.originalData = null
                        this.editIndex = null
                    }
                    
		            },
				    
		            submit(){
		                let data = $('#bonusSettingForm').serializeArray();
                    // let detail_error_check = false;
                    
		                if(this.boundary == 0 ){
		                  this.has_error = true;
		                }else{
                        this.has_error = false;
		                }
		                
                    // let detail_has_error = this.detail_has_error;
                    // const values = Object.values(this.items);
                    // values.map(function(v,k){
                    //     detail_has_error[k]['achieving_rate'] = v.achieving_rate == 0 ? true : false;
                    //     detail_has_error[k]['bonus_rate'] = v.bonus_rate == 0 ? true : false;
                    //     if(v.achieving_rate == 0 || v.bonus_rate == 0){
                    //         detail_error_check = true;
                    //     }
                    // });
                    // this.detail_has_error = detail_has_error;
				            
				            // detail_error_check =
						            this.detail_check();
		                
                    // console.log(!(this.has_error) && !(this.detail_error_check));
                    if( !(this.has_error) && !(this.detail_error_check) ){

                        $('#bonusSettingForm').submit();
                    }
		            },
                    detail_check(){
                    let detail_has_error = this.detail_has_error;
                    const values = Object.values(this.items);
                    let detail_error_check = false;
                    values.map(function(v,k){
                        detail_has_error[k]['achieving_rate'] = v.achieving_rate == 0 ? true : false;
                        detail_has_error[k]['bonus_rate'] = v.bonus_rate == 0 ? true : false;
                        if(v.achieving_rate == 0 || v.bonus_rate == 0){
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
		    
		    updated() {
		        // console.log('view updated')
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

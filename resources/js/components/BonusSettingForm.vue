<template>
	<div class="active tab-pane" id="settings">
		<!-- Horizontal Form -->
		<div class="box box-info">
			<div class="box-header with-border">
				<h3 class="box-title">當月獎金設定值</h3>
			</div>
			<!-- /.box-header -->
			<!-- form start -->
			<form class="form-horizontal" action='save' method='post' id='bonusSettingForm'>
				
				<div class="box-body">
					<div class="form-group">
						<input type='hidden' name="_token" :value='csrf'>
					</div>
					<div class="form-group" :class="{'has-error' : has_error}">
						<div v-if='type == "add"'>
							<label for="selectAddUserId" class="col-sm-2 control-label pull-left">新增業務</label>
							<div class="col-sm-3">
								<select class="form-control select2" style="width: 100%;" name='selectAddUserId'>
									<option v-for='add_user_list in addUserList' :value='add_user_list.id'>{{ add_user_list.name }}</option>
								</select>
							</div>
						</div>
						<label for="inputBoundary" class="col-sm-2 control-label pull-left">責任額</label>
						<div class="col-sm-3">
							<input type='number' name='boundary' class="form-control" id="inputBoundary" placeholder="個人責任額" v-model='boundary' >
							<span class="help-block" :class="{'hidden' : !has_error }">個人責任額 不能為0</span>
						</div>
					</div>
					<div class='form-group'>
						<div class="box-body">
							<table class="table table-bordered table-striped">
								<thead class="thead-light">
								<tr>
									<th width="1">#</th>
									<th width="30%">達成比例</th>
									<th width="30%">獎金比例</th>
									<th width="40%">Action</th>
								</tr>
								</thead>
								<tbody>
								<tr v-for="(item, index) in items" :key="index">
									<td>{{ index + 1 }}</td>
									<td>
										<span v-if="editIndex !== index">{{ item.achievingRate }}</span>
										<span v-if="editIndex === index">
              <input type='number' class="form-control form-control-sm" v-model="item.achievingRate">
            </span>
									</td>
									<td>
										<span v-if="editIndex !== index">{{ item.bounsRate }}</span>
										<span v-if="editIndex === index">
              <input type='number' class="form-control form-control-sm" v-model="item.bounsRate">
            </span>
									</td>
									<td>
            <span v-if="editIndex !== index">
		            <div v-for='(item, index ) in items'>
		              <input type='hidden' :name='"bounsRows["+index+"][achievingRate]"' :value='item.achievingRate'>
			            <input type='hidden' :name='"bounsRows["+index+"][bounsRate]"' :value='item.bounsRate'>
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
							
							<div class="col-3 offset-9 text-right my-3">
								<button type='button' v-show="!editIndex" @click="add" class="btn btn-success">新增</button>
							</div>
						
						</div>
					</div>
				
				</div>
				<!-- /.box-body -->
				<div class="box-footer">
					<button type="button" @click='submit()' class="btn btn-info pull-right">送出</button>
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
            csrf_token : String
        },
        filters: {
            money: (value) => new Intl.NumberFormat('ja-JP', { style: 'currency', currency: 'TWD',minimumFractionDigits: 0 }).format(value)
        },

        data() {
            return {
                has_error: false,
                editIndex: null,
                originalData: null,
                items: this.row.rateData ? this.row.rateData : [],
                boundary: this.row.boundary ? this.row.boundary : 0,
		            addUserList : this.add_user_lists,
		            csrf: this.csrf_token
            }
        },
        mounted: function() {
						$('.select2').select2();
						
				},
		    methods: {
		
		            add() {
		                console.log(this.boundary);
		                this.originalData = null
		                this.items.push({ achievingRate: '', bounsRate: '', })
		                this.editIndex = this.items.length - 1
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
		                this.items.splice(index, 1)
		                this.$set(this.items, 'penguin', 0)
				            console.log(this.originalData);
		                return
		            },
		
		            save(item) {
		                this.originalData = null
		                this.editIndex = null
		            },
		            submit(){
		                let data = $('#bonusSettingForm').serializeArray();
		                if(this.boundary == 0 ){
		                  this.has_error = true;
		                }else{
		                    this.has_error = false;
                        $('#bonusSettingForm').submit();
                    }
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
</style>

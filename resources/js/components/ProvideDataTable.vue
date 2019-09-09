<template>
	<div class='content table mdl-shadow--2dp mdl-data-table--selectable mdl-shadow--2dp col-md-12'>
		<!--{{top element}}-->
		<form action="#">
			<div class="col-sm-6 p-20">
				<div class="dataTables_length" id="example1_length">
					<label>Show
					<select name="show_length"  aria-controls="show_length"
					        class="form-control input-sm" v-model='show_item'
					        @change='show_length'
					>
						<option v-for='v in [50,100,200,500]' :value='v'>{{ v }}</option>
					</select>
					entries</label>
				</div>
			</div>
			<!--search Bar start-->
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label pull-right">
				<input class="mdl-textfield__input" type="text" id="search" @keyup="search" :value='search_str'>
				<label class="mdl-textfield__label" for="search" >搜尋</label>
			</div>
			<!--search Bar end-->
		</form>
		<!--{{level 1 head }}-->
		<div class='table head'>
				<div class='columm point col-md-1'>
					<label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="checkbox-all">
						<input type="checkbox" id="checkbox-all" class="mdl-checkbox__input" @click='select_all'>
					</label>
				</div>
				<div class='columm col-md-2'>
				業務
				<div class='arrow pull-right point' :class='sort_erp_user_id' data-sort='erp_user_id' @click="sorting"></div>
				</div>
				<div class='columm col-md-9'>
				案件名稱
				<div class='arrow pull-right point' :class='sort_campaign_name' data-sort='campaign_name' @click="sorting"></div>
				</div>
				<!--<div class='columm point col-md-4'>-->
				<!--	已選金額-->
				<!--</div>-->
				<div class='border-bottom'></div>
		</div>
		<!--{{level 1 body}}-->
		<div class='body text-center' id='table_body'>
			<!--{{loading}}-->
			<div class="mdl-spinner mdl-js-spinner loading is-active is-upgraded" :class='{"hidden" : loading}' id='loading'></div>
			<!--message-->
			<div class="loading" :class='{"hidden" : have_data}' id='nodata_message' >無資料</div>
			<div v-for='(groupByUsers,erpUserId) in row'>
				<div class='item' v-for='(groupByCampaigns,campaignId) in groupByUsers' @click='treeview_open'>
					<div class='columm col-md-1'>
						<label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" :for="`checkbox-level2-${erpUserId}-${campaignId}`">
							<input type="checkbox" :id="`checkbox-level2-${erpUserId}-${campaignId}`" class="mdl-checkbox__input level2" :data-id='campaignId' @click='select_leavel2_children'>
						</label>
					</div>
					<div class='columm col-md-2 point'>
						{{users[erpUserId]['name']}}
					</div>
					<div class='columm col-md-9 point'>
						{{groupByCampaigns[0]['campaign_name']}}
					</div>
					<!--<div class='columm col-md-4 point'>-->
					<!--	0-->
					<!--</div>-->
					<input type='hidden' name='campaignBonus' value='0'>
					<div class='border-bottom'></div>
					<div class='treeview-items'>
						<div class='head'>
							<div class='columm col-md-1'>
							</div>
							<div class='columm col-md-2'>
								月份
							</div>
							<div class='columm col-md-2'>
								媒體
							</div>
							<div class='columm col-md-2'>
								賣法
							</div>
							<div class='columm col-md-1'>
								%
							</div>
							<div class='columm col-md-2'>
								毛利
							</div>
							<div class='columm col-md-2'>
								獎金
							</div>
							<div class='border-bottom'></div>
						</div>
						<div class='body'>
							<div class='item' v-for='cue in groupByCampaigns' @click='select_self'>
								<div class='columm col-md-1'>
									<label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect children_select"
									       :for="`checkbox-${cue['id']}`">
										<input type="checkbox" :id="`checkbox-${cue['id']}`" class="mdl-checkbox__input" :value='`${cue["id"]}`' v-model="select_financial_id.indexOf(cue['id']) >= 0">
									</label>
								</div>
								<div class='columm col-md-2'>
									{{cue['set_date']}}
								</div>
								<div class='columm col-md-2'>
									{{cue['media_channel_name']}}
								</div>
								<div class='columm col-md-2'>
									{{cue['sell_type_name']}}
								</div>
								<div class='columm col-md-1'>
									{{cue['reach_rate'] == null ? 0 : cue['reach_rate'] }}%
								</div>
								<div class='columm col-md-2'>
									{{cue['profit']}}
								</div>
								<div class='columm col-md-2' :data-money="cue['profit']  > 0 ?  Math.round(cue['reach_rate'] * cue['profit'] / 100) : 0">
									{{  cue['profit']  > 0 ?  Math.round(cue['reach_rate'] * cue['profit'] / 100) : 0 }}
								</div>
								<input type='hidden' name='bonus' value=50>
								<div class='border-bottom'></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- footer-->
		<div class='footer'>
			<div class="col-sm-4"><div class="dataTables_info" id="example1_info" role="status" aria-live="polite">Showing {{first_item_num}} to {{last_item_num}} of {{total_item_num}} entries</div></div>
			<div class="col-sm-12 text-center">
				<pagination-component :arg='
				arg.paginate
				'></pagination-component>
				
			</div>
			<button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored pull-right" @click='post'>
				送出
			</button>
		</div>
	</div>
</template>

<script>
    import {mapState,mapMutations,mapActions} from 'vuex';

    function closeLoading() {
        componentHandler.upgradeDom();
        this.loading = true;
    }

    function messageController() {
        if (typeof this.total_item_num === undefined || this.total_item_num == 0) {
            this.have_data = false;
        } else {
            this.have_data = true;
        }
    }

    export default {
        props:{
            arg : Object,
        },
        computed: mapState(['change_date']),
        data() {
            return {
                csrf: this.arg.csrf_token,
                loading: true,
                have_data: true,
                paginate_count: this.arg.paginate_count,
                show_item : this.arg.show_item,
                search_str : this.arg.search_str,
                sort_erp_user_id : {
                    'sorting' : this.arg.sort_by != 'erp_user_id' ? true : false,
                    'sorting_asc' : this.arg.sort_by == 'erp_user_id' ? (this.arg.sort =='ASC' ? true : false) : false,
                    'sorting_desc' : this.arg.sort_by == 'erp_user_id' ? (this.arg.sort =='DESC' ? true : false) : false,
                },
                sort_campaign_name : {
                    'sorting' : this.arg.sort_by != 'campaign_name' ? true : false,
                    'sorting_asc' : this.arg.sort_by == 'campaign_name' ? (this.arg.sort =='ASC' ? true : false) : false,
                    'sorting_desc' : this.arg.sort_by == 'campaign_name' ? (this.arg.sort =='DESC' ? true : false) : false,
                },
                search_ev_time_stamp: 0,
                first_item_num: this.arg.first_item_num,
                last_item_num: this.arg.last_item_num,
                total_item_num: this.arg.total_item_num,
		            row : this.arg.row ? this.arg.row : {},
		            users : this.arg.users,
                total_bonus : 0,
                select_financial_id : this.arg.select_ids ? this.arg.select_ids : [],
		            original_select_financial_id : this.arg.original_select_financial_id,
                all_ids: this.arg.all_ids ? this.arg.all_ids : [],
                total_alreday_select_money : this.arg.total_alreday_select_money,
            }
        },
        methods: {
            sorting() {
                let target = $(event.target);
                
                let sorting = target.hasClass('sorting');
                let sorting_asc = target.hasClass('sorting_asc');
                let sorting_desc = target.hasClass('sorting_desc');
                
                let sort_name = target.data('sort');
                this.sortingReset(sort_name);
                console.log(sorting,sorting_asc,sorting_desc);
                if(sort_name == 'erp_user_id'){
                    if(sorting === true){
                        this.sort_erp_user_id.sorting_asc= true;
                    }
                    else if(sorting_asc === true){
                        this.sort_erp_user_id.sorting_desc = true;
                    }else if(sorting_desc === true){
                        this.sort_erp_user_id.sorting  = true;
                    }
                }else if (sort_name == 'campaign_name'){
                    if(sorting === true){
                        this.sort_campaign_name.sorting_asc= true;
                    }
                    else if(sorting_asc === true){
                        this.sort_campaign_name.sorting_desc = true;
                    }else if(sorting_desc === true){
                        this.sort_campaign_name.sorting  = true;
                    }
                }
                
                    //get url Parmas
                    let urlParmas = getUrlParmas();
                
                    urlParmas['sort_by'] = sort_name;
                    if(sorting){
                        //change parmas data
                        urlParmas['sort'] = 'ASC';
                    }
                    if(sorting_asc){
                        //change parmas data
                        urlParmas['sort'] = 'DESC';
                    }
                    if(sorting_desc){
                        //empty parmas data
                        delete urlParmas['sort_by'];
                        delete urlParmas['sort'];
                    };
		            
                    // //get data
                    this.getAjaxData(urlParmas).then(()=>{
                        closeLoading.call(this);
                    });
                    // //change Url parmas
                    changeUrlParmas(urlParmas);
            },
            sortingReset(sort_name){
                [this.sort_campaign_name,this.sort_erp_user_id].map(function(v,index){
                    v.sorting = false;
		                v.sorting_desc = false;
		                v.sorting_asc = false;
                });
                if(sort_name == 'erp_user_id'){
                  this.sort_campaign_name.sorting  = true;
                }else if (sort_name == 'campaign_name'){
                    this.sort_erp_user_id.sorting  = true;
                };
            },
            search(){
                let target = $(event.target);
                sleep(1000).then(()=>{
                      var now = +new Date();
                      if (now - this.search_ev_time_stamp < 1200) {
                          return;
                      }
                      this.search_ev_time_stamp = now;
                      //get url Parmas
                      let urlParmas = getUrlParmas();
                      
                      delete urlParmas['page'];
                      
                      if(target.val() === ''){
                          delete urlParmas['searchStr'];
                      }else{
                          urlParmas['searchStr'] = target.val();
                      }
		                 this.search_str = target.val();
                     this.getAjaxData(urlParmas).then(()=>{
                         closeLoading.call(this);
                         messageController.call(this);
                         this.checkAlreadySelect();
                     });
                     
                      changeUrlParmas(urlParmas);
                })
            },
            show_length(){
              let urlParmas = getUrlParmas();
              urlParmas.showItem = $(event.target).val();
               this.getAjaxData(urlParmas).then(()=>{
		               closeLoading.call(this);
                   messageController.call(this);
                });
              changeUrlParmas(urlParmas);
            },
		        select_all(){
              let checkStatus = $(event.target).prop("checked");
              
              this.toggleCheck(checkStatus, $('input:checkbox'));
                let all_ids = Object.assign([], this.all_ids);
                
                if (checkStatus) {
                  
                  this.select_financial_id = all_ids;
                  
                  this.ajaxCalculatFinancialBonus(this.select_financial_id);
                  
              } else {
                  this.select_financial_id = [];
                  $('#total_money').html(0);
                  console.log(this.select_financial_id);
              }
              
		        },
		        select_leavel2_children(){

                let checkStatus = $(event.target).prop("checked");
                let targetDom = $(event.target).parents('.item').find('.treeview-items').find('input:checkbox');
								
                this.toggleCheck(checkStatus, targetDom);
                this.selectIdValue(checkStatus,targetDom);
                this.calculatBonus(checkStatus,targetDom);
		        },
		        treeview_open(){
                let tragetDom = $(event.target).parent().children('.treeview-items');
                
                if (tragetDom.hasClass('is-show')) {
                    tragetDom.slideUp();
                    tragetDom.removeClass('is-show');
                } else {
                    tragetDom.slideDown();
                    tragetDom.addClass('is-show');
                }
		        },
		        select_self(){
              let targetDom = $(event.target).parent('.item').find('input:checkbox');
              
              let checkStatus = !targetDom.prop("checked");
              if(targetDom[0] == undefined){
                  targetDom = $(event.target);
                  checkStatus = targetDom.prop("checked");
              }
              
              
              if(targetDom[0].tagName == 'INPUT'){
                  this.toggleCheck(checkStatus, targetDom);
                  this.selectIdValue(checkStatus,targetDom);
                  this.calculatBonus(checkStatus,targetDom);
              }
              

              // let campaignBonus = $(this).parents('.item').find('input[name="campaignBonus"]');
              // let checkBoxDom = $(this).parent().find('input:checkbox');
							//
              // calculatBonus(checkBoxDom,campaignBonus);
		        },
            calculatBonus(checkStatus,targetDom){
                targetDom.map(function(index,v){
                 
	                let this_money = $(v).parent().parent().parent('.item').find('div[data-money]').data('money');
	                
	                let nowTotal =  parseInt($('#total_money').html());
	                if (checkStatus) {
	                    $('#total_money').html(nowTotal + this_money);
	                } else {
	                    $('#total_money').html(nowTotal - this_money);
	                }
                });
		        },
            toggleCheck(checkStatus, targetDom){
                if (checkStatus) {
                    targetDom.prop("checked", true);
                    targetDom.parents('label').addClass('is-checked');
                } else {
                    targetDom.prop("checked", false);
                    targetDom.parents('label').removeClass('is-checked');
                }
            },
            selectIdValue(checkStatus,targetDom){
                let select_financial_id =  Object.assign([], this.select_financial_id);
                
                if(checkStatus){
                    targetDom.map(function(index){
                        index = select_financial_id.indexOf(parseInt($(this).val()));
                        
                        if (index == -1) {
                            select_financial_id.push(parseInt($(this).val()));
                        }
                    });
                   
                }else{
                    targetDom.map(function(index){
                        index = select_financial_id.indexOf(parseInt($(this).val()));
                        
                        if (index > -1) {
                            select_financial_id.splice(index, 1);
                        }
                    });
                }
                this.select_financial_id = select_financial_id;
                
            },
		        changeDateDate(date){
                let urlParmas = getUrlParmas();
                urlParmas['startDate'] = date.replace("/", "-")+'-01';
                urlParmas['type'] = 'view';
                // //get data
                this.getAjaxData(urlParmas).then(()=>{
                    closeLoading.call(this);
                });
		        },
            getAjaxData(urlParmas) {
                this.loading = false;
                this.row = [];
                let thisVue = this;
                return axios({
                    url: '/financial/provide/getAjaxData',
                    method: 'post',
		                data:urlParmas,
                    headers: {
                        'Content-Type': 'application/json',
                    }
                }).then(
		                (res)=>{
                        thisVue.row = res.data.row;
                        let paginate = res.data.paginate;
                        thisVue.total_item_num = paginate.total;
                        thisVue.first_item_num = paginate.from;
                        thisVue.last_item_num = paginate.to;
		                }
                ).catch(err => console.error(err));
            },
            ajaxCalculatFinancialBonus(selectIds) {
                let parmas = {'select_financial_ids': JSON.stringify(selectIds)};
                
                return axios({
                    url: '/financial/provide/ajaxCalculatFinancialBonus',
                    method: 'post',
                    data:parmas,
                    headers: {
                        'Content-Type': 'application/json',
                    }
                }).then(
                    (res)=>{
                        // thisVue.row = res.data.row;
		                    $('#total_money').html(res.data);
		                    
                    }
                ).catch(err => console.error(err));
            },
            post(){
              //创建form表单
		            var temp_form = document.createElement("form");
						    temp_form.action = 'post';
						    //如需打开新窗口，form的target属性要设置为'_blank'
						    // temp_form.target = "_blank";
						    temp_form.method = "post";
						    temp_form.style.display = "none";
						    //添加参数
                let paramters = {
                    '_token' : this.csrf,
                    'select_financial_ids' : [this.select_financial_id],
                    'original_select_financial_ids' : [this.original_select_financial_id]
                };
                // paramters.map(function(v){
                //     console.log(v);
                // });
                Object.keys(paramters).forEach(function(key) {
                  var opt = document.createElement("textarea");
                      opt.name = key;
                      opt.value = paramters[key];
                      temp_form.appendChild(opt);
                });
						    // for ( (item,key) in paramters) {
						    //     var opt = document.createElement("textarea");
						    //     opt.name = key;
						    //     opt.value = item;
						    //     temp_form.appendChild(opt);
						    // }
						    document.body.appendChild(temp_form);
						    // //提交数据
						    temp_form.submit();
            },
		        checkAlreadySelect(){
                let thisVue = this;
                $('.mdl-checkbox__input.level2').map(function(k,v){
                    let checkChilren = true;
                    $(v).parents('.item').children('.treeview-items').find('input[type="checkbox"]').map(function (k,v) {
                        checkChilren = $(v).prop("checked");
                    });
                    if(checkChilren){
                        thisVue.toggleCheck(checkChilren,$(v));
                    }
                });
		        }
        },
        mounted() {
            //檢查已經選取的資料 css勾選
            this.checkAlreadySelect();
            //顯示已顯取金額
            $('#total_money').html(this.total_alreday_select_money);
        },
        watch:{
            change_date: {
                immediate: true,    // 这句重要
                handler (val, oldVal) {
                    if(oldVal !== undefined) {
                        // this.changeDateDate(val);
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>

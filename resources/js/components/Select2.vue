<template>
		<select class="form-control select2" :id='id' :multiple='multiple' :data-placeholder='placeholder' :disabled='disabled'
		        style="width: 100%;">
			<option :value='item.id' v-for='item in row' :selected='!disabled'> {{item.name}} </option>
		</select>
		<!-- /.user-select-block -->
</template>

<script>
    import {mapState} from 'vuex';
    export default {
        name: "select2",
        props: {
			    id:String,
			    multiple:Boolean,
	            disabled:Boolean,
                selected:Boolean,
			    placeholder:String,
			    row:Array
        },
        data() {
            return {}
        },
        methods:{
            updateSelectToVux(domId){
              switch(domId){
                case 'select_user':
                    this.changeUserId($('#'+domId+'').val());
                    break;
                case 'select_groups' :
                    this.changeSaleGroupId($('#'+domId+'').val());
                    break;
              case 'select_currency':
                      this.$store.state.currency = $('#'+domId+'').val();
                  break;
              }
            },
            changeUserId(ids) {
                this.$store.commit('changeUserId', ids);
            },
            changeSaleGroupId(ids) {
                this.$store.commit('changeSaleGroupId', ids);
            },
        },
        computed: mapState(['user_ids','sale_group_ids']),
        mounted: function(){
            
            $('#'+this.id+'').select2();
		        
            var domId = this.id;
            var thisVue = this;
            
            thisVue.updateSelectToVux(domId);
            $('#'+this.id+'').on('change', function (e) {
                thisVue.updateSelectToVux(domId);
            });
            var evTimeStamp = 0;
            $('.row').on('click','input[name="selectType"]',function(v,k){
                let now = new Date();
                
                if (now - evTimeStamp < 100) {
                    return;
                }
                
                evTimeStamp = now;
                
                let select_user = $('#select_user');
                let select_groups = $('#select_groups');
                let type = $(this).data('type');
                switch (type){
                    case 'select_user':
                        select_user.attr('disabled',false);
                        select_groups.attr('disabled',true).val(null).trigger("change");
                        select_groups.next().find('li.select2-selection__choice').remove();
                        select_groups.next().find('.select2-search__field').attr('placeholder','選擇團隊').css('width','415px');
                        thisVue.changeSaleGroupId($('#'+domId+'').val());
                        break;
                    case 'select_groups':
                        select_groups.attr('disabled',false);
                        select_user.attr('disabled',true).val(null).trigger("change");
                        select_user.next().find('li.select2-selection__choice').remove();
                        select_user.next().find('.select2-search__field').attr('placeholder','選擇成員').css('width','415px');
                        thisVue.changeUserId($('#'+domId+'').val());
                        break;
                }
            });
            // $('#'+this.id+'').on('select2:select', function (e) {
            //     var data = e.params.data;
            //     console.log(data);
            // });
            // $('#'+this.id+'').on('select2:unselect', function (e) {
            //     var data = e.params.data;
            //     console.log(data);
            // });
        },
        watch:{
            // change_date: {
            //     immediate: true,    // 这句重要
            //     handler (val, oldVal) {
            //         if(oldVal !== undefined) {
            //
            //             console.log(val,oldVal);
            //         }
            //     }
            // }
        }
    }
</script>

<style scoped>

</style>

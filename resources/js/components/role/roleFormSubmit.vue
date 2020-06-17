<template>
    <button type="button" :id='domid' class="btn btn-block btn-success" @click='post'>送出</button>
</template>

<script>
    import {mapState,mapMutations,mapActions,mapGetters} from 'vuex';
    export default {
        name: "roleFormSubmit",
        props: {
            domid:String,
            csrf: String,
            post_action_url:String,
            role_id:String,
        },
        data() {
            return {}
        },
        computed: {
            ...mapGetters('dataTable',['getPermissionTableSelect'])
        },
        methods:{
            post(){
                //创建form表单
                var temp_form = document.createElement("form");
                temp_form.action = this.post_action_url;
                //如需打开新窗口，form的target属性要设置为'_blank'
                // temp_form.target = "_blank";
                temp_form.method = "post";
                temp_form.style.display = "none";
                //添加参数
                let paramters = {
                    '_token' : this.csrf,
                    'permission_ids' : this.getPermissionTableSelect,
                    'id': this.role_id,
                    'name':$('input[name="name"]').val(),
                    'label':$('input[name="label"]').val()
                };

                // paramters.map(function(v){
                // });
                Object.keys(paramters).forEach(function(key) {
                    var opt = document.createElement("textarea");
                    opt.name = key;
                    opt.value = paramters[key];
                    temp_form.appendChild(opt);
                });
                document.body.appendChild(temp_form);
                // //提交数据
                temp_form.submit();
            },
        },
        mounted: function(){
        },
        watch:{
            // change_date: {
            //     immediate: true,
            //     handler (val, oldVal) {
            //         if(oldVal !== undefined) {
            //
            //         }
            //     }
            // }
        }
    }
</script>

<style scoped>

</style>

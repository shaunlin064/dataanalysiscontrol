<template>
	<button type="button" :id='domid' class="btn btn-block btn-success" @click='post'>送出</button>
</template>

<script>
    import {mapActions, mapState , mapGetters} from 'vuex';
    import * as types from "../store/types";
    export default {
        name: "ProvideSubmit",
        props: {
            domid: String,
            csrf: String,
            post_action_url: String,
        },
        data() {
            return {}
        },
        computed: {
            ...mapState('dataTable',['table_select']),
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
                    'provide_bonus_beyond' : this.table_select.bonus_beyond_list,
                    'provide_bonus' : this.table_select.provide_bonus_list,
                    'provide_sale_groups_bonus' : this.table_select.provide_groups_list,
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

                document.body.appendChild(temp_form);
                // //提交数据
                temp_form.submit();
            },
        },
        mounted: function(){

        },
        watch:{

        }
    }
</script>

<style scoped>

</style>

<template>
	<div>
		<i v-if='loading' class="fa fa-spin fa-refresh" style="font-size: 3em; padding: 7px;"></i>
		<h3 v-else :id='domid'>{{total}}</h3>
	</div>
</template>

<script>
    import {mapState} from 'vuex';
    export default {
        name: "count_total",
        props: {
            domid:String,
        },
        data() {
            return {
              total : 0,
            }
        },
        computed: {
            ...mapState(['bonus_total_money','sale_group_total_money','loading']),
        },
                methods:{
                  getTotal(){
                      return this.bonus_total_money+this.sale_group_total_money;
                  }
                },
                mounted: function(){
                },
                watch:{
                    bonus_total_money: {
                        immediate: true,    // 这句重要
                        handler (val, oldVal) {
                            if(oldVal !== undefined && val !== oldVal) {
                                this.total = this.getTotal();
                            }
                        }
                    },
                    sale_group_total_money: {
                        immediate: true,    // 这句重要
                        handler (val, oldVal) {
                            if(oldVal !== undefined && val !== oldVal) {
                                this.total = this.getTotal();
                            }
                        }
                    }
                }
    }
</script>

<style scoped>

</style>

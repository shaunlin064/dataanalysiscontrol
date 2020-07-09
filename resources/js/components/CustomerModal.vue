<template>
    <div>
        <div class="modal fade" :class='show ? "in" : ""' :id='target_dom_id'>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" @click='closeCustomerModal'>
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"  id="staticBackdropLabel">客戶分析圖表</h4>
                    </div>
                    <div class="modal-body">
                        <chart-customer-precentage-profit
                            :table_id='"customer_precentage_profit"'
                            :labels='[]'
                        ></chart-customer-precentage-profit>
                        <load-item style='margin: 25% 50%;right: 0px;'></load-item>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
            <div class="modal-backdrop fade" :style='show ? styleList : {}' @click='closeCustomerModal'></div>
        </div>
    </div>
</template>

<script>
   import {mapState, mapMutations, mapActions, mapGetters} from 'vuex';

       export default {
           name: "customerModal",
           props: {

           },
           data() {
               return {
                   show:false,
                   styleList: {
                       display: 'block'
                   },
                   dom_id: 'customerModal',
                   target_dom_id : 'customerModalTemple'
               }
           },
           computed: {
                   ...mapState([]),
           },
           beforeMount: function () {
           },
           mounted: function () {

           },
           created: function () {
               bus.$on('openCustomerModal', this.openCustomerModal);
           },
           methods: {
               openCustomerModal(){
                   this.show = true;
               },
               closeCustomerModal(){
                   this.show = false;
               }
           },
           updated() {
               // console.log('view updated')
           },
           watch: {
           }
       }
</script>

<style scoped>
    .modal-dialog{
        width: 85%;
        z-index: 1;
    }
    .modal-backdrop{
        display: none;
        width: 100%;
        height: 100%;
        position: absolute;
        z-index: 0;
    }
    .in{
        padding-right: 15px;
        display: block;
    }
</style>

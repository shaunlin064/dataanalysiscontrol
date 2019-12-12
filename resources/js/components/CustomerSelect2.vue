<template>
    <select class="form-control select2" :id='id' :multiple='multiple' :data-placeholder='placeholder' :disabled='disabled'
            style="width: 100%;">
        <option :value='item.id' v-for='item in row' :selected='selected ? selected : item.id==0'> {{item.name}} </option>
    </select>
    <!-- /.user-select-block -->
</template>

<script>
    import {mapState} from 'vuex';
    export default {
        name: "select2-customer",
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
                    case 'agency_ids':
                        this.changeAgencyIds($('#'+domId+'').val());
                        break;
                    case 'client_ids':
                        this.changeClientIds($('#'+domId+'').val());
                        break;
                    case 'media_companies_ids':
                        this.changeMediaCompaniesIds($('#'+domId+'').val());
                        break;
                    case 'medias_names':
                        this.changeMediasNames($('#'+domId+'').val());
                        break;
                }
            },
            changeAgencyIds(ids) {
                this.$store.commit('changeAgencyIds', ids);
            },
            changeClientIds(ids) {
                this.$store.commit('changeClientIds', ids);
            },
            changeMediaCompaniesIds(ids) {
                this.$store.commit('changeMediaCompaniesIds', ids);
            },
            changeMediasNames(ids) {
                this.$store.commit('changeMediasNames', ids);
            },
        },
        computed: mapState(['agency_ids','client_ids','media_companies_ids','medias_names']),
        mounted: function(){
            $('#'+this.id+'').select2();

            var domId = this.id;
            var thisVue = this;

            thisVue.updateSelectToVux(domId);
            $('#'+this.id+'').on('change', function (e) {
                thisVue.updateSelectToVux(domId);
            });
        },
        watch:{
        }
    }
</script>

<style scoped>

</style>

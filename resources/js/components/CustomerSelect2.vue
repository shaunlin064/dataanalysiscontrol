<template>
    <select class="form-control select2" :id='dom_id' :multiple='multiple' :data-placeholder='placeholder'
            :disabled='disabled'
            style="width: 100%;">
        <option :value='item.id' v-for='item in row' :selected='selected ? selected : item.id==0'> {{item.name}}
        </option>
    </select>
    <!-- /.user-select-block -->
</template>

<script>
    import {mapState, mapMutations, mapActions} from 'vuex';
    import * as types from "../store/types";
    export default {
        name: "select2-customer",
        props: {
            dom_id: String,
            multiple: Boolean,
            disabled: Boolean,
            selected: Boolean,
            placeholder: String,
            row: Array
        },
        data() {
            return {}
        },
        methods: {
            ...mapActions('select',[types.CHANGE_AGENCY_IDS,types.CHANGE_CLIENT_IDS,types.CHANGE_MEDIA_COMPANIES_IDS,types.CHANGE_MEDIAS_NAMES]),
            updateSelectToVux(domId) {
                let ids = $('#' + domId + '').val();
                switch (domId) {
                    case 'agency_ids':
                        this[types.CHANGE_AGENCY_IDS](ids);
                        break;
                    case 'client_ids':
                        this[types.CHANGE_CLIENT_IDS](ids);
                        break;
                    case 'media_companies_ids':
                        this[types.CHANGE_MEDIA_COMPANIES_IDS](ids);
                        break;
                    case 'medias_names':
                        this[types.CHANGE_MEDIAS_NAMES](ids);
                        break;
                }
            },
        },
        computed: {
        },
        mounted: function () {
            var vue = this;
            $('#' + vue.dom_id + '').select2();
            vue.updateSelectToVux(vue.dom_id);
            $('#' + vue.dom_id + '').on('change', function (e) {
                vue.updateSelectToVux(vue.dom_id);
            });
        }
        ,
        watch: {}
    }
</script>

<style scoped>

</style>

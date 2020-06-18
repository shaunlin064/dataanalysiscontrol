<template>
    <select class="form-control select2" :id='id' :multiple='multiple' :data-placeholder='placeholder'
            :disabled='disabled'
            style="width: 100%;">
        <option :value='item.id' v-for='item in row' :selected='!disabled'> {{item.name}}</option>
    </select>
    <!-- /.user-select-block -->
</template>

<script>
    import {mapActions, mapState} from 'vuex';
    import * as types from "../store/types";

    export default {
        name: "select2",
        props: {
            id: String,
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
            ...mapActions('exchangeRate', [types.CHANGE_CURRENCY]),
            ...mapActions('select', [types.CHANGE_USER_ID, types.CHANGE_SALE_GROUP_ID]),
            updateSelectToVux(domId) {

                switch (domId) {
                    case 'select_user':
                        this.changeUserId($('#' + domId + '').val());
                        break;
                    case 'select_groups' :
                        this.changeSaleGroupId($('#' + domId + '').val());
                        break;
                    case 'select_currency':
                        this[types.CHANGE_CURRENCY]($('#' + domId + '').val());
                        break;
                }
            },
            changeUserId(ids) {
                this[types.CHANGE_USER_ID](ids);
            },
            changeSaleGroupId(ids) {
                this[types.CHANGE_SALE_GROUP_ID](ids);
            },
        },
        computed: {
            ...mapState('select', ['user_ids', 'sale_group_ids']),
            ...mapState('exchangeRate', ['currency']),
        },
        mounted: function () {
            var thisVue = this;
            var domId = thisVue.id;

            $('#' + domId + '').select2();

            thisVue.updateSelectToVux(domId);
            $('#' + domId + '').on('change', function (e) {
                thisVue.updateSelectToVux(domId);
            });
            var evTimeStamp = 0;
            $('.row').on('click', 'input[name="selectType"]', function (v, k) {
                let now = new Date();

                if (now - evTimeStamp < 100) {
                    return;
                }

                evTimeStamp = now;

                let select_user = $('#select_user');
                let select_groups = $('#select_groups');
                let type = $(this).data('type');

                switch (type) {
                    case 'select_user':
                        select_user.attr('disabled', false);
                        select_groups.attr('disabled', true).val(null).trigger("change");
                        select_groups.next().find('li.select2-selection__choice').remove();
                        select_groups.next().find('.select2-search__field').attr('placeholder', '選擇團隊').css('width', '415px');
                        thisVue.changeSaleGroupId($('#' + domId + '').val());
                        break;
                    case 'select_groups':
                        select_groups.attr('disabled', false);
                        select_user.attr('disabled', true).val(null).trigger("change");
                        select_user.next().find('li.select2-selection__choice').remove();
                        select_user.next().find('.select2-search__field').attr('placeholder', '選擇成員').css('width', '415px');
                        thisVue.changeUserId($('#' + domId + '').val());
                        break;
                }
            });
        },
        watch: {}
    }
</script>

<style scoped>

</style>

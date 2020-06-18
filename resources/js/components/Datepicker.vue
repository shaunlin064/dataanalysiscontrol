<template>
    <div class="input-group date">
        <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
        </div>
        <input type="text" class="form-control pull-right" :id=dom_id :value=date :name=dom_id>
    </div>
</template>

<script>
    import {mapState, mapMutations, mapActions, mapGetters} from 'vuex';
    import * as types from '../store/types'
    export default {
        name: "Datepicker",
        props: {
            dom_id: String,
            date: String
        },
        data() {
            return {
                date_start: this.date ? this.date : new Date(),
            }
        },
        computed: {
        },
        methods: {
            ...mapActions('datePick',[types.CHANGE_DATE]),
        },
        created: function () {
            let d = new Date(this.date);
            this[types.CHANGE_DATE](moment(d).format('YYYY/MM'));
        },
        mounted: function () {
            let vue = this;
            let d = this.date ? this.date : this.date_start;
            let m = moment(d).format('MM');
            let y = moment(d).format('YYYY');
            let td = moment(d).format('DD');

            let domEle = $('#' + this.dom_id + '');

            domEle.datepicker({
                autoclose: true,
                startView: "months",
                minViewMode: "months",
                defaultViewDate: {
                    year: y,
                    month: m,
                    day: td
                },
                format: 'yyyy/mm',
                language: 'zh-TW',
            }).on("change", function () {
                vue[types.CHANGE_DATE](this.value);
            });
        }
    }
</script>

<style scoped>

</style>

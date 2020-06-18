<template>
	<!-- Date range -->
	<div class="form-group">
		<label>Date range:</label>

		<div class="input-group">
			<div class="input-group-addon">
				<i class="fa fa-calendar"></i>
			</div>
			<input type="text" class="form-control pull-right" :id='dom_id' name="daterange" >
		</div>
		<!-- /.input group -->
	</div>
</template>

<script>
    import {mapState, mapMutations, mapActions} from 'vuex';
    import * as types from '../store/types';

    export default {
        name: "date-range",
        props: {
            input_start_date : String,
            input_end_date : String,
            dom_id : String,
        },
        computed: {
            ...mapState('dateRange',['start_date','end_date']),
        },
        data() {
            return {
                daterangepickerObj: {},
            }
        },
        mounted: function(){
            //Date range picker
            var dateRoot =  $('#' + this.dom_id + '');
            var vue = this;
            vue[types.CHANGE_DATE_RANGE]([vue.input_start_date,vue.input_end_date]);
            vue.daterangepickerObj = dateRoot.daterangepicker({
                showDropdowns: true,
                batchMode: "months-range",
                locale: { format: 'YYYY/MM' },
                startDate: vue.start_date,
                endDate: vue.end_date,
            });

            $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
                let starDate = picker.startDate.format('YYYY-MM-01');
                let endDate = picker.endDate.format('YYYY-MM-01');
                vue[types.CHANGE_DATE_RANGE]([starDate,endDate]);
            });
        },
        methods: {
            ...mapActions('dateRange',[types.CHANGE_DATE_RANGE]),
            update(){
                $('#' + this.dom_id + '').val(`${this.start_date.replace("-01", "")} - ${this.end_date.replace("-01", "")}`);
            },
        },
        watch: {
            start_date: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined) {
                        this.update();
                    }
                }
            },
            end_date: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined ) {
                        this.update();
                    }
                }
            },
        }
    }
</script>

<style scoped>

</style>

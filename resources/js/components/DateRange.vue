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
    
    export default {
        name: "date-range",
        props: {
            input_start_date : String,
            input_end_date : String,
            dom_id : String,
        },
        computed: {...mapState(['start_date','end_date'])},
        data() {
            return {
                starDate : this.input_start_date,
                endDate : this.input_end_date,
                daterangepickerObj: {},
            }
        },
        mounted: function(){
            //Date range picker
            var dateRoot =  $('#' + this.dom_id + '');
            var thisVue = this;
            thisVue.changeDate();
            this.daterangepickerObj = dateRoot.daterangepicker({
                showDropdowns: true,
                batchMode: "months-range",
                locale: { format: 'YYYY/MM' },
                startDate: this.$store.state.start_date,
                endDate: this.$store.state.end_date,
            });
            // thisVue.changeDate();
            $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
                thisVue.starDate = picker.startDate.format('YYYY-MM-01');
                thisVue.endDate = picker.endDate.format('YYYY-MM-01');
                thisVue.changeDate();
            });
        },
        methods: {
            changeDate() {
                this.$store.commit('changeDateRange', [this.starDate,this.endDate]);
            },
            update(){
                $('#' + this.dom_id + '').val(`${this.$store.state.start_date.replace("-01", "")} - ${this.$store.state.end_date.replace("-01", "")}`);
            },
        },
        watch: {
            start_date: {
                immediate: true,    // 这句重要
                handler(val, oldVal) {
                    if (oldVal !== undefined) {
                        this.update();
                    }
                }
            },
            end_date: {
                immediate: true,    // 这句重要
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

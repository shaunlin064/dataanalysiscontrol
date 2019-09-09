<template>
	<!-- Date range -->
	<div class="form-group">
		<label>Date range:</label>
		
		<div class="input-group">
			<div class="input-group-addon">
				<i class="fa fa-calendar"></i>
			</div>
			<input type="text" class="form-control pull-right" id="reservation">
		</div>
		<!-- /.input group -->
	</div>
</template>

<script>
    export default {
        name: "date-range",
        props: {
            start_date : String,
            end_date : String,
        },
        data() {
            return {
                starDate : this.start_date,
                endDate : this.end_date,
            }
        },
        mounted: function(){
            //Date range picker
            var dateRoot = $('#reservation');
            var thisVue = this;
            dateRoot.daterangepicker({
                showDropdowns: true,
                batchMode: "months-range",
                locale: { format: 'YYYY/MM' },
                startDate: this.starDate,
                endDate: this.endDate,
            });
		        
            dateRoot.on('apply.daterangepicker', function(ev, picker) {

                thisVue.starDate = picker.startDate.format('YYYYMM');
                thisVue.endDate = picker.endDate.format('YYYYMM');
                
								//
                // historyDom.hide();
                // historyDom.map(function(){
                //     let date = $(this).data('history-range');
								//
                //     if((date > rangeStart && date < rangeEnd) || date == rangeStart || date == rangeEnd){
                //         $(this).show();
                //     }
                // });
            });
        },
    }
</script>

<style scoped>

</style>

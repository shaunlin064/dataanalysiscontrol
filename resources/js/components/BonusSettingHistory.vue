<template>
	<!-- /.tab-pane -->
	<div class="tab-pane" :class="{'active':active}" id="hirstory">
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
		<!-- /.form group -->
		<div class="box box-warning" :class="{'collapsed-box' : key > 0}" v-for='(item,key) in items' :data-history-range=formate(item.set_date)>
			<div class="box-header with-border">
				<h3 class="box-title">{{item.set_date}}</h3>
				
				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
				</div>
				<!-- /.box-tools -->
			</div>
			<!-- /.box-header -->
			<div class="box-body">
				<h4>責任額
					<span class="pull-right">{{item.boundary | money}}</span>
				</h4>
				<div class="box">
					<div class="box-header">
						<h3 class="box-title">獎金級距</h3>
					</div>
					<!-- /.box-header -->
					<div class="box-body no-padding">
						<table class="table table-condensed">
							<tbody><tr>
								<th style="width: 10px">#</th>
								<th>達成比例</th>
								<th>獎金比例</th>
							</tr>
							<tr v-for='(children, index) in item.levels'>
								<td>{{index+1}}.</td>
								<td><span class='badge bg-light-blue'>{{ children.achieving_rate }}%</span></td>
								<td><span class="badge bg-red">{{ children.bouns_rate }}%</span></td>
							</tr>
							</tbody></table>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</div>
			<!-- /.box-body -->
		</div>
		<!-- /.box -->
	</div>
	<!-- /.tab-pane -->
</template>
<script>

    require('../../../public/adminLte_componets/bootstrap-daterangepicker/daterangepicker');
    export default {
        props: { items: Array ,active: Boolean},
        filters: {
            money: (value) => new Intl.NumberFormat('ja-JP', { style: 'currency', currency: 'TWD',minimumFractionDigits: 0 }).format(value)
        },
        mounted: function(){
            //Date range picker
		        var dateRoot = $('#reservation');
		        var historyDom = $('div[data-history-range]');
            dateRoot.daterangepicker({
                showDropdowns: true,
                batchMode: "months-range",
                locale: { format: 'YYYY/MM' },
                startDate: "2018-12",
            });
						
            var rangeStart = '';
            var rangeEnd = '';

            dateRoot.on('apply.daterangepicker', function(ev, picker) {
                rangeStart = picker.startDate.format('YYYYMM');
                rangeEnd = picker.endDate.format('YYYYMM');
                
                historyDom.hide();
                historyDom.map(function(){
                    let date = $(this).data('history-range');
                    
                    if((date > rangeStart && date < rangeEnd) || date == rangeStart || date == rangeEnd){
                        $(this).show();
                    }
                });
            });
        },
        created() {
        
			  },
		    methods:{
            formate(v){
                
                v = v.replace('-','');
                v = v.replace('-01','');
                
                return v;
            }
		    }
    }
    
</script>

<style scoped>

</style>

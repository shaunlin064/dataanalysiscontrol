<template>
	<!-- Profile Image -->
	<div class="box box-primary">
		<div class="box-body box-profile">
<!--			<img class="profile-user-img img-responsive img-circle" src="/img/user4-128x128.jpg" alt="User profile picture">-->
			
			<h3 class="profile-username text-center">{{user_data.name}}</h3>
			
			<p class="text-muted text-center">{{user_data.title}}</p>
			
			<ul class="list-group list-group-unbordered">
				<li class="list-group-item">
					<b>上月業績毛利</b> <a class="pull-right">{{user_data.lastMonthProfit | money }}</a>
				</li>
				<li class="list-group-item">
					<b>本月業績毛利</b> <a class="pull-right">{{user_data.thisMonthProfit | money}}</a>
				</li>
				<li class="list-group-item">
					<b>最高業績毛利</b> <a class="pull-right">{{user_data.historyMonthProfit | money}}</a>
				</li>
			</ul>
<!--			<a href="#" class="btn btn-primary btn-block"><b>Follow</b></a>-->
		</div>
		<!-- /.box-body -->
	</div>
	<!-- /.box -->
</template>

<script>
    export default {
        props: {user_data : Object},
        filters: {
            money: (value) => new Intl.NumberFormat('ja-JP', { style: 'currency', currency: 'TWD',minimumFractionDigits: 0 }).format(value)
        },
        mounted() {
            // console.log(this.user_data);
        }
    }
</script>

<style scoped>

</style>

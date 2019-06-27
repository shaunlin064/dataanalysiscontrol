<template>
	<div class="small-box" :class=color>
		<div class="inner">
			<h3 v-if="vuex_field === 'profit'" v-html=profit></h3>
			<h3 v-else-if="vuex_field === 'bounce_rate'" >{{bounce_rate}}<sup style="font-size: 20px">%</sup></h3>
			<h3 v-else v-html=number></h3>
			<p v-html=text></p>
		</div>
		<div class="icon">
			<i :class=icon></i>
		</div>
	</div>
</template>

<script>
    import {mapState,mapMutations,mapActions} from 'vuex';
    export default {
        name: "BoxInfo",
		    props:{
            color: String,
				    icon:String,
				    number:String,
            vuex_field:String,
            text:String,
		    },
		    filters: {
            money: (value) => new Intl.NumberFormat('ja-JP', { style: 'currency', currency: 'TWD',minimumFractionDigits: 0 }).format(value)
        },
        computed: mapState(['profit','bounce_rate']),
		    methods:{

		    }
    }
</script>

<style scoped>

</style>

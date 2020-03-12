<template>
	<div class="small-box" :class=color>
		<div class="inner">
			<h3 v-if="vuex_field === 'profit'" v-html=profit></h3>
			<h3 v-else-if="vuex_field === 'bonus_rate'" >{{bonus_rate}}<sup style="font-size: 20px">%</sup> <span v-if='bonus_direct !== 0'>+ {{bonus_direct}}</span></h3>
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
        computed: mapState(['profit','bonus_rate','bonus_direct']),
		    mounted: function(){
            if(this.$attrs.profit !== undefined){
                this.$store.commit('changeBox',{value:this.$attrs.profit,field:'profit'});
            }
            if(this.$attrs.bonus_rate !== undefined){
                this.$store.commit('changeBox',{value:this.$attrs.bonus_rate,field:'bonus_rate'});
            }
            if(this.$attrs.bonus_direct !== undefined){
                this.$store.commit('changeBox',{value:this.$attrs.bonus_direct,field:'bonus_direct'});
            }
            
		    },
		    methods:{
						
		    }
    }
</script>

<style scoped>

</style>

<template>
	<div class="info-box customer-fit" :class=color>
		<span class="info-box-icon"><i :class=icon></i></span>
		<div class="info-box-content">
			<span class="info-box-text" v-html=title></span>
			<span class="info-box-number" v-if="vuex_field === 'bonus_next_level'" v-html=bonus_next_amount></span>
			<span class="info-box-number" v-else v-html=number></span>
			<div class="progress">
				<div class="progress-bar" v-if="vuex_field === 'bonus_next_level'" :style="{width: bonus_next_percentage +'%'}"></div>
				<div class="progress-bar" v-else :style="{width: percentage +'%'}"></div>
			</div>
			<span class="progress-description" v-if="vuex_field === 'bonus_next_level'">
        已達成{{bonus_next_percentage}}%
			</span>
			<span class="progress-description" v-else>
        已達成{{percentage}}%
			</span>
		</div>
	</div>
</template>

<script>
    import {mapState,mapMutations,mapActions} from 'vuex';
    export default {
        name: "BoxProgress",
		    props:{
            color: String,
            icon:String,
				    title:String,
            vuex_field:String,
            number:String,
            percentage:Number,
		    },
        computed: mapState(['bonus_next_amount','bonus_next_percentage']),
        mounted: function(){
            if(this.$attrs.bonus_next_amount !== undefined){
                this.$store.commit('changeBox',{value:this.$attrs.bonus_next_amount,field:'bonus_next_amount'});
            }
            if(this.$attrs.bonus_next_percentage !== undefined){
                this.$store.commit('changeBox',{value:this.$attrs.bonus_next_percentage,field:'bonus_next_percentage'});
            }
        },
    }
</script>

<style scoped>

</style>

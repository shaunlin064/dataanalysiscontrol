<template>
	<div class='container'>
		<p>{{ count }}</p>
		<p>
			<button @click='increment'>+1</button>
		</p>
	</div>
</template>

<script>
	import {mapState,mapMutations} from 'vuex';
    export default {
        computed: mapState(['count']),
		    methods:{
            ...mapMutations(['increment'])
        }
    };
</script>

<style scoped>

</style>

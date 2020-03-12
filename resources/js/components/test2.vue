<template>
	<div>The count from that other unrelated componet is : {{count}}</div>
</template>

<script>
    import {mapState,mapMutations} from 'vuex';
    export default {
        computed: mapState(['count']),
    }
</script>

<style scoped>

</style>

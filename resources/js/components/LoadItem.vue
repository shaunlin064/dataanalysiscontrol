<template>
    <div class="lds-spinner" style='right: 90px;top: 0;' v-if='loading'><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
</template>

<script>
    import {mapState, mapMutations, mapActions, mapGetters} from 'vuex';
    export default {
        name: "LoadItem",
        computed: {
            ...mapState(['loading']),
        },
    }

</script>

<style scoped>

</style>

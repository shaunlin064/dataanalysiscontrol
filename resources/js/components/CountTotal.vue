<template>
    <div>
        <i v-if='loading' class="fa fa-spin fa-refresh" style="font-size: 3em; padding: 7px;"></i>
        <h3 v-else :id='domid'>{{total}}</h3>
    </div>
</template>

<script>
    import {mapActions, mapState , mapGetters} from 'vuex';
    import * as types from "../store/types";
    export default {
        name: "count_total",
        props: {
            domid: String,
        },
        data() {
            return {
                total: 0,
            }
        },
        computed: {
            ...mapState('financial',['bonus_total_money', 'sale_group_total_money', 'provide_total_money']),
            ...mapState(['loading'])
        },
        methods: {
            getTotal() {
                return this.bonus_total_money + this.sale_group_total_money;
            }
        },
        mounted: function () {
        },
        watch: {
            provide_total_money: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined) {
                        this.total = this.provide_total_money;
                    }
                }
            },
            bonus_total_money: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== oldVal) {
                        this.total = this.getTotal();
                    }
                }
            },
            sale_group_total_money: {
                immediate: true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== oldVal) {
                        this.total = this.getTotal();
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>

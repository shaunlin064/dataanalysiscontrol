<template>

</template>

<script>
    import {mapState, mapMutations, mapActions, mapGetters} from 'vuex';

    export default {
        name: "FinancialProvideAjaxCenter",
        props: {
            csrf: String,
        },
        data() {
            return {
                ajax_url : '/financial/provide/getAjaxProvideData',
            }
        },
        computed: {
            ...mapState(['start_date', 'end_date', 'user_ids', 'sale_group_ids', 'loading']),
        },
        methods: {
            tableClear: function () {
                $('table').map(function (e, v) {
                    $.fn.dataTable.Api(v).clear();
                    $.fn.dataTable.Api(v).draw();
                });
            },
            getData() {
                let data = {
                    _token: this.csrf,
                    startDate: this.$store.state.start_date,
                    endDate: this.$store.state.end_date,
                    saleGroupIds: this.$store.state.sale_group_ids,
                    userIds: this.$store.state.user_ids,
                };

                if ((data.saleGroupIds == '' && data.userIds == '') || data._token === undefined) {
                    return false;
                }

                this.$store.state.loading = true;
                this.tableClear();

                axios.post(this.ajax_url, data).then(
                    response => {
                        this.$store.state.loading = false;

                        let bonus_total_money = parseInt(0);
                        let sale_group_total_money = parseInt(0);

                        if (response.data) {

                            response.data.provide_bonus_list.map(function (v) {
                                bonus_total_money += parseInt(v.provide_money);
                            });

                            response.data.provide_groups_list.map(function (v) {
                                sale_group_total_money += parseInt(v.provide_money);
                            });

                            this.$store.state.bonus_total_money = bonus_total_money;
                            this.$store.state.sale_group_total_money = sale_group_total_money;
                            this.$store.state.provide_bonus_list =  response.data.provide_bonus_list;
                            this.$store.state.provide_groups_list =  response.data.provide_groups_list;

                        }
                    },
                    err => {

                        reject(err);
                    }
                );
            },
        },
        mounted: function () {
        },
        watch: {
            start_date: {
                immediate: true,    // 这句重要
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '') {
                        this.getData();
                    }
                }
            },
            end_date: {
                immediate: true,    // 这句重要
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '') {
                        this.getData();
                    }
                }
            },
            user_ids: {
                immediate: true,    // 这句重要
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '') {
                        this.getData();
                    }
                }
            },
            sale_group_ids: {
                immediate: true,// 这句重要
                // lazy:true,
                handler(val, oldVal) {
                    if (oldVal !== undefined && val !== '') {
                        this.getData();
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>

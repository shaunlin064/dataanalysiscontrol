<template>
    <div class="input-group">
        <div class='col-xs-4 mt-5'>
            <div class="btn-group dropright">
                <button type="button" class="btn btn-dropbox dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">月
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="#" @click="click('lM')">前月</a></li>
                    <li><a href="#" @click="click('tM')">本月</a></li>
                </ul>
            </div>
        </div>
        <div class='col-xs-4 mt-5'>
            <div class="btn-group dropright">
                <button type="button" class="btn btn-dropbox dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">季
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="#" @click="click('q1')">Q1</a></li>
                    <li><a href="#" @click="click('q2')">Q2</a></li>
                    <li><a href="#" @click="click('q3')">Q3</a></li>
                    <li><a href="#" @click="click('q4')">Q4</a></li>
                </ul>
            </div>
        </div>
        <div class='col-xs-4 mt-5'>
            <div class="btn-group dropright">
                <button type="button" class="btn btn-dropbox dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">年
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="#" @click="click('lY')">前年度</a></li>
                    <li><a href="#" @click="click('tY')">本年度</a></li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapState, mapMutations, mapActions} from 'vuex';

    export default {
        name: "SelectButtonGroup",
        props: {
            input_start_date : String,
            input_end_date : String,
        },
        computed: {...mapState(['start_date','end_date'])},
        data() {
            return {
            }
        },
        mounted: function(){
        
        },
        methods: {
            update(){
            
            },
            click(dataSelectType) {
                let d = new Date();
                let dateStrStart;
                let dateStrEnd;
                let year = d.getFullYear();
                let month = d.getMonth();

                switch (dataSelectType) {
                    case 'lY':
                        dateStrStart = `${d.getFullYear() - 1}-01-01`;
                        dateStrEnd = `${d.getFullYear() - 1}-12-01`;
                        break;
                    case 'lM':
                        if (d.getMonth() === 0) {
                            year = d.getFullYear() - 1;
                            month = 12;
                        }
                        dateStrStart = `${year}-${month}-01`;
                        dateStrEnd = `${year}-${month}-01`;
                        break;
                    case 'tY':
                        dateStrStart = `${d.getFullYear()}-01-01`;
                        dateStrEnd = `${d.getFullYear()}-12-01`;
                        break;
                    case 'tM':
                        dateStrStart = `${year}-${month+1}-01`;
                        dateStrEnd = `${year}-${month+1}-01`;
                        break;
                    case 'q1':
                        dateStrStart = `${year}-01-01`;
                        dateStrEnd = `${year}-03-01`;
                        break;
                    case 'q2':
                        dateStrStart = `${year}-04-01`;
                        dateStrEnd = `${year}-06-01`;
                        break;
                    case 'q3':
                        dateStrStart = `${year}-07-01`;
                        dateStrEnd = `${year}-09-01`;
                        break;
                    case 'q4':
                        dateStrStart = `${year}-10-01`;
                        dateStrEnd = `${year}-12-01`;
                        break;
                }
                
                this.$store.commit('changeDateRange', [dateStrStart,dateStrEnd]);
            }
        },
    }
</script>

<style scoped>

</style>

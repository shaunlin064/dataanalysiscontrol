<template>
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active" v-show="type !== 'view'" data-step="1" data-intro="設定責任額頁面"><a href="#settings"
                                                                                              data-toggle="tab">設定</a>
            </li>
            <li :class="{'active':type == 'view'}" v-if="type == 'edit' || type == 'view'" data-step="8"
                data-intro="責任額歷史資料檢視"><a href="#hirstory" data-toggle="tab">歷史資料</a></li>
        </ul>
        <div class="tab-content">
            <bonus-setting-form v-if="type !== 'view'" :row='setting' :already_set_user_ids='already_set_user_ids'
                                :form_action='form_action' :type='type' :add_user_lists='add_user_lists'
                                :csrf_token='csrf_token'></bonus-setting-form>
            <bonus-setting-history v-if="type == 'edit' || type == 'view'" :items='history'
                                   :active="type != 'edit'"></bonus-setting-history>
        </div>
        <!-- /.tab-content -->
    </div>
    <!-- /.nav-tabs-custom -->
</template>

<script>
    export default {
        props: {arg: Object},
        data() {
            return {
                type: this.arg.type,
                setting: this.arg.setting ? this.arg.setting : {},
                history: this.arg.history ? this.arg.history : {},
                add_user_lists: this.arg.add_user_lists ? this.arg.add_user_lists : [],
                form_action: this.arg.form_action ? this.arg.form_action : 'save',
                csrf_token: this.arg.csrf_token,
                already_set_user_ids: this.arg.alreadySetUserIds ? this.arg.alreadySetUserIds : [],
            }
        },
        mounted() {
            // console.log(this.arg);
        }
    }
</script>

<style scoped>

</style>

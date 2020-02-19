<template>
    <div class="modal fade" id="edit-sub-menu-template">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit SubMenu</h4>
                </div>
                <div class="modal-body">
                    <form role="form" action='./menuSubPost' method='post'>
                        <!-- text input -->
                        <input type='hidden' name="_token" :value=csrf>
                        <div class="form-group">
                            <label for="icon_class">頁面開啟方式</label>
                            <!-- select -->
                            <select class="form-control" name="target" v-model="row.target">
                                <option value='_self' >相同視窗</option>
                                <option value='_blank'>另開視窗</option>
                            </select>
                            <input name="menu_id" type="hidden" class="form-control" placeholder="catalogue ..." :value=row.menu_id>
                            <input name="id" type="hidden" class="form-control" placeholder="catalogue ..." :value=row.id>
                        </div>
                        <div class="form-group">
                            <label>Name</label>
                            <input name="name" type="text" class="form-control" placeholder="name ..." :value=row.name>
                        </div>
                        <div class="form-group">
                            <label>Priority</label>
                            <input name="priority" type="text" class="form-control" placeholder="priority ..." :value=row.priority>
                        </div>
                        <div class="form-group">
                            <label for="icon_class">Icon（ <a href="/adminlte/icons" target="_blank">使用 Adminlte 圖標庫</a>）</label>
                            <input name="icon" type="text" class="form-control" placeholder="icon ..." :value=row.icon>
                        </div>
                        <div class="form-group">
                            <label for="icon_class">Url</label>
                            <input name="url" type="text" class="form-control" placeholder="url ..." :value=row.url>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</template>

<script>
    export default {
        name: "menuSubForm",
        props: {
            csrf: String,
            row: Object,
        },
    }
</script>

<style scoped>

</style>

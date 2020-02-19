<template>
    <div class="modal fade" id='edit-menu-template'>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Menu</h4>
                </div>
                <div class="modal-body">
                    <form role="form" action='./menuPost' method='post'>
                        <!-- text input -->
                        <input type='hidden' name="_token" :value=csrf>
                        <div class="form-group">
                            <label>Name</label>
                            <input name="name" type="text" class="form-control" placeholder="name ..." :value=row.name>
                        </div>
                        <div class="form-group">
                            <label for="priority">Priority</label>
                            <input name="priority" type="text" class="form-control" placeholder="priority ..." :value=row.priority>
                        </div>
                        <div class="form-group">
                            <label for="icon">Icon（ <a href="/adminlte/icons" target="_blank">使用 Adminlte 圖標庫</a>）</label>
                            <input name="icon" type="text" class="form-control" placeholder="icon ..." :value=row.icon>
                        </div>
                        <div class="form-group">
                            <label for="region">Region</label>
                            <input name="region" type="text" class="form-control" placeholder="region ..." :value=row.region>
                        </div>
                        <div class="form-group">
                            <label for="catalogue">Catalogue</label>
                            <input name="catalogue" type="text" class="form-control" placeholder="catalogue ..." :value=row.catalogue>
                            <input name="id" type="hidden" class="form-control" placeholder="catalogue ..." :value=row.id>
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
    import {mapState, mapMutations, mapActions, mapGetters} from 'vuex';
    
    export default {
        
        name: "menuForm",
        props: {
            csrf: String,
            row: Object,
        },
    }
</script>

<style scoped>

</style>

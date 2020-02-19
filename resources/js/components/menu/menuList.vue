<template>
    <div class="col-xs-12">
        <!--    add new Menu template-->
        <div class="modal fade" id="add-menu-template">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Add New Menu</h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" action='./menuPost' method='post'>
                            <!-- text input -->
                            <input type='hidden' name="_token" :value=csrf>
                            <div class="form-group">
                                <label>Name</label>
                                <input name="name" type="text" class="form-control" placeholder="name ...">
                            </div>
                            <div class="form-group">
                                <label>Priority</label>
                                <input name="priority" type="text" class="form-control" placeholder="priority ...">
                            </div>
                            <div class="form-group">
                                <label for="icon">Icon（ <a href="/adminlte/icons" target="_blank">使用 Adminlte 圖標庫</a>）</label>
                                <input name="icon" type="text" class="form-control" placeholder="icon ...">
                            </div>
                            <div class="form-group">
                                <label for="region">Region</label>
                                <input name="region" type="text" class="form-control" placeholder="region ...">
                            </div>
                            <div class="form-group">
                                <label for="catalogue" >Catalogue</label>
                                <input name="catalogue" type="text" class="form-control" placeholder="catalogue ...">
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
        <!--    /.add new Menu template-->
        <!-- add new SubMenu template-->
        <div class="modal fade" id="add-sub-menu-template">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Edit SubMenu</h4>
                    </div>
                    <div class="modal-body">
                        <form action='./menuSubPost' method='post' role="form">
                            <!-- text input -->
                            <input type='hidden' name="_token" :value=csrf>
                            <div class="form-group">
                                <label>Name</label>
                                <input name="name" type="text" class="form-control" placeholder="name ..." value=''>
                            </div>
                            <div class="form-group">
                                <label>Priority</label>
                                <input name="priority" type="text" class="form-control" placeholder="priority ..." value=''>
                            </div>
                            <div class="form-group">
                                <label for="icon">Icon（ <a href="/adminlte/icons" target="_blank">使用 Adminlte 圖標庫</a>）</label>
                                <input name="icon" type="text" class="form-control" placeholder="icon ..." value=''>
                            </div>
                            <div class="form-group">
                                <label for="url">Url</label>
                                <input name="url" type="text" class="form-control" placeholder="url ..." value=''>
                            </div>
                            <div class="form-group">
                                <label for="target">Open In</label>
                                <!-- select -->
                                <select class="form-control" name="target">
                                    <option value='_self' >Same Tab/Window</option>
                                    <option value='_blank' >New Tab/Window</option>
                                </select>
                                <input name="menu_id" type="hidden" class="form-control" placeholder="catalogue ..." value=''>
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
        <!-- add new SubMenu template-->
        <menu-form
            :csrf=csrf
            :row=menuData
        ></menu-form>
        <menu-sub-form
            :csrf=csrf
            :row=menuSubData
        ></menu-sub-form>
        <div class="box">
            <div class="box-header">
                <h3 class="box-title pull-left">清單</h3>
                <div class='col-md-2 pull-right'>
                    <button type="button" id='addNewMenu' data-toggle="modal" data-target="#add-menu-template" class="btn btn-block btn-success">新增</button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table id="menuTable" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th></th>
                        <th>name</th>
                        <th>priority</th>
                        <th>icon</th>
                        <th>region</th>
                        <th>catalogue</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th></th>
                        <th>name</th>
                        <th>priority</th>
                        <th>icon</th>
                        <th>region</th>
                        <th>catalogue</th>
                        <th>Action</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</template>

<script>
    import {mapState, mapMutations, mapActions, mapGetters} from 'vuex';
    
    export default {
        name: "menuList",
        props: {
            csrf: String,
            row: Array,
        },
        data() {
            return {
                menus:[],
                menuData: {},
                menuSubs:[],
                menuSubData:{},
            }
        },
        methods: {
            format(d) {
                // `d` is the original data object for the row
                let subMenu = '';
                let vue = this;
                Object.keys(d.sub_menu).forEach(key=>{
                    // vue.menuSubs[d.sub_menu[key]['id']] = d.sub_menu[key];
                    subMenu += `<tr>
                            <td>${d.sub_menu[key]['name']}</td>
                            <td>${d.sub_menu[key]['priority']}</td>
                            <td>${d.sub_menu[key]['url']}</td>
                            <td>${d.sub_menu[key]['icon']}</td>
                            <td>${d.sub_menu[key]['target']}</td>
                            <td>
<button type="button" data-toggle="modal" data-action="editMenuSub" data-id='${d.sub_menu[key]['id']}' data-target="#edit-sub-menu-template" class="btn btn-primary btn-flat">編輯</button>
<button data-action='deleteMenuSub' type="button" data-id='${d.sub_menu[key]['id']}' class="btn btn-danger btn-flat">刪除</button>
</td>
</tr>`;
                });

                return `<table class='table table-bordered table-striped dataTable' cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">
                            <thead>
                            <tr>
                                <th>名稱</th>
                                <th>priority</th>
                                <th>url</th>
                                <th>icon</th>
                                <th>target</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        <tbody>
                        ${subMenu}
                        </tbody>
                        </table>`;
            },
            post(URL, PARAMTERS) {
                //创建form表单
                var temp_form = document.createElement("form");
                temp_form.action = URL;
                //如需打开新窗口，form的target属性要设置为'_blank'
                temp_form.target = "_self";
                temp_form.method = "post";
                temp_form.style.display = "none";
        
                //添加参数
                for (var item in PARAMTERS) {
                    var opt = document.createElement("textarea");
                    opt.name = PARAMTERS[item].name;
                    opt.value = PARAMTERS[item].value;
                    temp_form.appendChild(opt);
                }
        
                document.body.appendChild(temp_form);
        
                //提交数据
                temp_form.submit();
        
            },
            fillMenus(rowData){
                Object.keys(rowData).forEach(key=> {
                    let meun = rowData[key];
                    let menuSub = meun['sub_menu'];
                    this.menus[meun['id']] = meun;
                    
                    Object.keys(menuSub).forEach(key=> {
                        this.menuSubs[menuSub[key]['id']] = menuSub[key];
                    });
                });
            },
        },
        beforeMount: function () {
            
            var vue = this;
            var rowData = vue.row;
            this.fillMenus(rowData);
            
            $(document).ready(function () {

                var dataTable = $('#menuTable').DataTable({
                    'paging'      : true,
                    'ordering'    : true,
                    'responsive': true,
                    'info'        : true,
                    'autoWidth'   : false,
                    "aLengthMenu" : [25, 50, 100], //更改顯示記錄數選項
                    "oLanguage": {
                        "emptyTable"    : "目前沒有任何（匹配的）資料。",
                        "sProcessing":   "處理中...",
                        "sLengthMenu":   "顯示 _MENU_ 項結果",
                        "sZeroRecords":  "沒有資料",
                        "sInfo":         "顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項",
                        "sInfoEmpty":    "顯示第 0 至 0 項結果，共 0 項",
                        "sInfoFiltered": "(從 _MAX_ 項結果過濾)",
                        "sInfoPostFix":  "",
                        "sSearch":       "搜索:",
                        "sUrl":          "",
                        "oPaginate": {
                            "sFirst":    "首頁",
                            "sPrevious": "上頁",
                            "sNext":     "下頁",
                            "sLast":     "尾頁"
                        }
                    },
                    columns: [
                        {
                            "className":      'details-control',
                            "orderable":      false,
                            "data":           null,
                            "defaultContent": ''
                        },
                        { data: "name" },
                        { data: "priority" },
                        { data: 'icon'},
                        { data: 'region'},
                        { data: 'catalogue'},
                        { data: 'id',
                            render: function (data) {
                                if(data != 0){
                                    return `
<button data-action='editMenu' type="button" data-toggle="modal" data-target="#edit-menu-template"  data-id="${data}" class="btn btn-primary btn-flat">編輯</button>
<button type="button"  data-action='addMenuSub' data-id="${data}" data-toggle="modal" data-target="#add-sub-menu-template" class="btn btn-success btn-flat">新增子選單</button>
<button data-action='deleteMenu' type="button" data-id='${data}' class="btn btn-danger btn-flat">刪除</button>`;
                                }else{
                                    return `<button data-action='AddMenu' type="button" class="btn btn-success btn-flat">送出</button>`;
                                }
                            }
                        }
                    ],
                });
                
                dataTable.clear();
                dataTable.rows.add( rowData );
                dataTable.draw();
                
                $('#menuTable tbody').on('click', 'td.details-control', function () {

                    var tr = $(this).closest('tr');
                    var row = dataTable.row( tr );
                    
                    if ( row.child.isShown() ) {
                        // This row is already open - close it
                        row.child.hide();
                        tr.removeClass('shown');
                    }
                    else {
                        // Open this row
                        row.child( vue.format(row.data()) ).show();
                        tr.addClass('shown');
                    }
                    
                });
                
                $('.col-xs-12').on('click','.btn',function(){
                    
                    if( $(this).data('action') !== undefined){
                        
                        let btnAction = $(this).data('action');
                        
                        switch(btnAction){
                            case 'editMenu':
                                vue.menuData = vue.menus[$(this).data('id')];
                                break;
                            case 'deleteMenu':
                                if(confirm('確認是否要刪除menu 連同子項目也會被刪除')){
                                    vue.post('menuDelete',[{'name':'_token','value':vue.csrf},{'name':'id','value':$(this).data('id')}]); }
                                break;
                            case 'addMenuSub':
                                $('#add-sub-menu-template').find('input[name="menu_id"]').val($(this).data('id'));
                                break;
                            case 'editMenuSub':
                                vue.menuSubData = vue.menuSubs[$(this).data('id')];
                                break;
                            case 'deleteMenuSub':
                                /*do edit open*/
                                if(confirm('確認是否要刪除')) {
                                    vue.post('menuSubDelete', [{'name': '_token', 'value': vue.csrf}, {
                                        'name': 'id',
                                        'value': $(this).data('id')
                                    }]);
                                }
                                break;
                        }
                    };
                });
            });
        }
    }
</script>

<style scoped>

</style>

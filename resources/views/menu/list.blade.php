<?php
    /**
     * Created by PhpStorm.
     * User: shaun
     * Date: 2020/2/12
     * Time: 下午3:35
     */
    ?>
@extends('layout')

@section('title','DAC | title')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        Menu設定
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">title</li>
    </ol>
@endsection

@extends('headerbar')


<!-- Content Wrapper. Contains page content -->
@section('content')
    <div class="row" id='menuSetting'>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{--add new Menu template--}}
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
                                @csrf
                                <div class="form-group">
                                    <label>Name</label>
                                    <input name="name" type="text" class="form-control" placeholder="name ...">
                                </div>
                                <div class="form-group">
                                    <label>Priority</label>
                                    <input name="priority" type="text" class="form-control" placeholder="priority ...">
                                </div>
                                <div class="form-group">
                                    <label for="icon_class">Icon（ <a href="/adminlte/icons" target="_blank">使用 Adminlte 圖標庫</a>）</label>
                                    <input name="icon" type="text" class="form-control" placeholder="icon ...">
                                </div>
                                <div class="form-group">
                                    <label for="icon_class">Region</label>
                                    <input name="region" type="text" class="form-control" placeholder="region ...">
                                </div>
                                <div class="form-group">
                                    <label for="icon_class">Catalogue</label>
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
        {{--/.add new Menu template--}}
        {{--add new SubMenu template--}}
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
                            @csrf
                            <div class="form-group">
                                <label>Name</label>
                                <input name="name" type="text" class="form-control" placeholder="name ..." value=''>
                            </div>
                            <div class="form-group">
                                <label>Priority</label>
                                <input name="priority" type="text" class="form-control" placeholder="priority ..." value=''>
                            </div>
                            <div class="form-group">
                                <label for="icon_class">Icon（ <a href="/adminlte/icons" target="_blank">使用 Adminlte 圖標庫</a>）</label>
                                <input name="icon" type="text" class="form-control" placeholder="icon ..." value=''>
                            </div>
                            <div class="form-group">
                                <label for="icon_class">Url</label>
                                <input name="url" type="text" class="form-control" placeholder="url ..." value=''>
                            </div>
                            <div class="form-group">
                                <label for="icon_class">Open In</label>
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
        {{--add new SubMenu template--}}
        <div class="col-xs-12">
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
        <!-- /.col -->
        {{--Menu template--}}
        @foreach($listdata as $menu)
            <div class="modal fade" id="edit-menu-template-{{$menu->id}}">
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
                                @csrf
                                <div class="form-group">
                                    <label>Name</label>
                                    <input name="name" type="text" class="form-control" placeholder="name ..." value='{{$menu->name}}'>
                                </div>
                                <div class="form-group">
                                    <label>Priority</label>
                                    <input name="priority" type="text" class="form-control" placeholder="priority ..." value='{{$menu->priority}}'>
                                </div>
                                <div class="form-group">
                                    <label for="icon_class">Icon（ <a href="/adminlte/icons" target="_blank">使用 Adminlte 圖標庫</a>）</label>
                                    <input name="icon" type="text" class="form-control" placeholder="icon ..." value='{{$menu->icon}}'>
                                </div>
                                <div class="form-group">
                                    <label for="icon_class">Region</label>
                                    <input name="region" type="text" class="form-control" placeholder="region ..." value='{{$menu->region}}'>
                                </div>
                                <div class="form-group">
                                    <label for="icon_class">Catalogue</label>
                                    <input name="catalogue" type="text" class="form-control" placeholder="catalogue ..." value='{{$menu->catalogue}}'>
                                    <input name="id" type="hidden" class="form-control" placeholder="catalogue ..." value='{{$menu->id}}'>
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
        @foreach($menu->submenu as $subMenu)
            <div class="modal fade" id="edit-sub-menu-template-{{$subMenu->id}}">
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
                                @csrf
                                <div class="form-group">
                                    <label>Name</label>
                                    <input name="name" type="text" class="form-control" placeholder="name ..." value='{{$subMenu->name}}'>
                                </div>
                                <div class="form-group">
                                    <label>Priority</label>
                                    <input name="priority" type="text" class="form-control" placeholder="priority ..." value='{{$subMenu->priority}}'>
                                </div>
                                <div class="form-group">
                                    <label for="icon_class">Icon（ <a href="/adminlte/icons" target="_blank">使用 Adminlte 圖標庫</a>）</label>
                                    <input name="icon" type="text" class="form-control" placeholder="icon ..." value='{{$subMenu->icon}}'>
                                </div>
                                <div class="form-group">
                                    <label for="icon_class">Url</label>
                                    <input name="url" type="text" class="form-control" placeholder="url ..." value='{{$subMenu->url}}'>
                                </div>
                                <div class="form-group">
                                    <label for="icon_class">Open In</label>
                                    <!-- select -->
                                    <select class="form-control" name="target">
                                        <option value='_self' @if($subMenu->target == '_self') selected @endif>Same Tab/Window</option>
                                        <option value='_blank' @if($subMenu->target == '_blank') selected @endif>New Tab/Window</option>
                                    </select>
                                    <input name="menu_id" type="hidden" class="form-control" placeholder="catalogue ..." value='{{$menu->id}}'>
                                    <input name="id" type="hidden" class="form-control" placeholder="catalogue ..." value='{{$subMenu->id}}'>
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
            @endforeach
        @endforeach
        {{--/.Menu template--}}
    </div>
    <!-- /.row -->
@endsection

@section('script')

    <!-- page script -->
    <script>
        $(function () {
                {{--var departments = {!! json_encode(session('department')) !!};--}}
                function format ( d ) {
                    // `d` is the original data object for the row
                    let subMenu = '';
                    Object.keys(d.sub_menu).forEach(key=>{
                        subMenu += `<tr>
                            <td>${d.sub_menu[key]['name']}</td>
                            <td>${d.sub_menu[key]['priority']}</td>
                            <td>${d.sub_menu[key]['url']}</td>
                            <td>${d.sub_menu[key]['icon']}</td>
                            <td>${d.sub_menu[key]['target']}</td>
                            <td>
<button type="button" data-toggle="modal" data-target="#edit-sub-menu-template-${d.sub_menu[key]['id']}" class="btn btn-primary btn-flat">編輯</button>
<button data-action='DeleteMenuSub' type="button" data-id='${d.sub_menu[key]['id']}' class="btn btn-danger btn-flat">刪除</button>
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
                }
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
<button data-action='EditMenu' type="button" data-toggle="modal" data-target="#edit-menu-template-${data}"  class="btn btn-primary btn-flat">編輯</button>
<button type="button"  data-action='AddMenuSub' data-id="${data}" data-toggle="modal" data-target="#add-sub-menu-template" class="btn btn-success btn-flat">新增子選單</button>
<button data-action='DeleteMenu' type="button" data-id='${data}' class="btn btn-danger btn-flat">刪除</button>`;
                                }else{
                                    return `<button data-action='AddMenu' type="button" class="btn btn-success btn-flat">送出</button>`;
                                }
                            }
                        }
                    ],
                });
            let listdata = {!! json_encode($listdata)  !!};

            dataTable.clear();
            dataTable.rows.add( listdata );
            dataTable.draw();

            // $('#menuSetting').on('click','#addNewMenu',function(){
            //
            //     dataTable.row.add({
            //         'id': 0,
            //         'name': `<input name='name'>`,
            //         'priority': `<input name='priority'>`,
            //         'icon': `<input name='icon'>`,
            //         'new_class': `<input name='new_class'>`,
            //         'region': `<input name='region'>`,
            //         'catalogue': `<input name='catalogue'>`,
            //         'sub_menu':[
            //             {'name' : `<input name='sub_menu[name]'>`,
            //             'icon' : `<input name='sub_menu[icon]'>`,
            //             'url' : `<input name='sub_menu[url]'>`,}
            //         ]
            //     } ).draw( false );
            //
            //     console.log(dataTable.data());
            // });
            function Post(URL, PARAMTERS) {
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

            }

            $('#menuSetting').on('click','.btn',function(){
               if( $(this).data('action') !== undefined){
                   let btnAction = $(this).data('action');

                 switch(btnAction){
                     case 'DeleteMenu':
                         Post('menuDelete',[{'name':'_token','value':'{{csrf_token()}}'},{'name':'id','value':$(this).data('id')}]);
                         break;
                     case 'AddMenuSub':
                         $('#add-sub-menu-template').find('input[name="menu_id"]').val($(this).data('id'));
                         break;
                     case 'DeleteMenuSub':
                         /*do edit open*/
                         Post('menuSubDelete',[{'name':'_token','value':'{{csrf_token()}}'},{'name':'id','value':$(this).data('id')}]);
                         break;
                 }
               };
            });

            // Add event listener for opening and closing details
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
                    row.child( format(row.data()) ).show();
                    tr.addClass('shown');
                }
            } );
        });
    </script>
@endsection

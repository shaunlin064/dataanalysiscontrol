<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-18
	 * Time: 14:09
	 */

?>

@extends('layout')

@section('title','DAC | 獎金檢視')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        獎金檢視
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">獎金檢視頁面</li>
    </ol>
@endsection

@extends('headerbar')


<!-- Content Wrapper. Contains page content -->
@section('content')
    <div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">人員清單</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table id="bonusTable" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>名稱</th>
                        <th>部門</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>名稱</th>
                        <th>部門</th>
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
    </div>
    <!-- /.row -->
@endsection

@section('script')

    <!-- page script -->
    <script>
        $(function () {
            var users = {!! json_encode(session('users')) !!};
            var dataTable = $('#bonusTable').DataTable({
                'paging'      : true,
                'ordering'    : true,
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
                        data: "erp_user_id",
                        render: function(data, type, row){
                            return users[data]['name'];
                        }
                    },
                    { data: 'sale_groups_name'},
                    { data: 'erp_user_id',
                        render: function (data,type,row) {
                            return `<a href='/bonus/review/view/${data}'>
<button type="button" class="btn btn-primary btn-flat">檢視</button>
</a>`;
                        }
                    }
                ],
            });
            let listdata = {!! json_encode($row)  !!};
            console.log(listdata);
            dataTable.clear();
            dataTable.rows.add( listdata );
            dataTable.draw();
        })
    </script>
@endsection




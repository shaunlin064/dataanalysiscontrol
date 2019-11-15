<?php
    /**
     * Created by PhpStorm.
     * User: shaun
     * Date: 2019/10/24
     * Time: 4:36 下午
     */
?>

@extends('layout')

@section('title','DAC | 權限角色')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        群組權限
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">群組權限</li>
    </ol>
@endsection

@extends('headerbar')


<!-- Content Wrapper. Contains page content -->
@section('content')
    <div class="row" id='roleList'>
        <div class='col-md-2 pull-right'>
            <button type="button" id='addNewRole' class="btn btn-block btn-success">新增</button>
        </div>
        <div class="col-xs-12">
            <simple-data-table-componet
                :table_id='"role_list"'
                :table_head='"群組權限表"'
                :table_title='["群組名稱","備註","action"]'
                :row = '{!! json_encode($roleList) !!}'
                :csrf= '"{{csrf_token()}}"'
                :columns = '{!!json_encode($columns)!!}'
            ></simple-data-table-componet>
        </div>
    </div>
    <!-- /.row -->
@endsection

@section('script')

    <!-- page script -->
    <script>
        $(function () {
            $('#roleList').on('click','#addNewRole',function(){
                window.open('/system/roleAdd');
            });
        })
    </script>
@endsection

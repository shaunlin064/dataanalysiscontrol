<?php
    /**
     * Created by PhpStorm.
     * User: shaun
     * Date: 2019/10/25
     * Time: 3:03 下午
     */
?>

@extends('layout')

@section('新增群組權限','DAC | title')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        新增群組權限
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">新增群組權限</li>
    </ol>
@endsection

@extends('headerbar')


<!-- Content Wrapper. Contains page content -->
@section('content')
    <div class="row">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">編輯使用者群組</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form class="form-horizontal"  action='/system/roleUserPost' method='post' id='bonusSettingForm'>
                <div class="box-body">
                    <div class="form-group">
                        <input type='hidden' name="_token" value={{csrf_token()}}>
                    </div>
                    <div class="form-group">
                        <div>

                            <input type='hidden' name='id' value='{{$user->id}}'>

                            <label class="col-sm-2 control-label pull-left">名稱</label>
                            <label class='col-sm-2 control-label' >{{$user->name}}</label>
                        </div>
                    </div>
                    <simple-data-table-componet
                        :table_id='"role_list"'
                        :table_head='"群組列表"'
                        :table_title='["選擇","名稱","備註"]'
                        :type = '"select"'
                        :select_id='{{ json_encode( $user->roles->pluck('id')) }}'
                        :row = '{!! json_encode($rolesList) !!}'
                        :csrf= '"{{csrf_token()}}"'
                        :columns = '{!!json_encode($columns)!!}'
                    ></simple-data-table-componet>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <button type="submit" class="btn btn-info pull-right">送出</button>
                </div>
                <!-- /.box-footer -->
            </form>
        </div>

        <!-- /.col -->
    </div>
    <!-- /.row -->
@endsection

@section('script')

    <!-- page script -->
    <script>
        $(function () {
        })
    </script>
@endsection

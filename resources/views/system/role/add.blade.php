<?php
    /**
     * Created by PhpStorm.
     * User: shaun
     * Date: 2019/10/25
     * Time: 10:19 上午
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
                <h3 class="box-title">新增群組</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form class="form-horizontal"  action='/system/rolePost' method='post' id='bonusSettingForm'>
                <div class="box-body">
                    <div class="form-group">
                        <input type='hidden' name="_token" value={{csrf_token()}}>
                    </div>
                    <div class="form-group">
                        <div>
                            @if( ( $type ?? '') == 'edit')
                                <input type='hidden' name='id' value='{{$role->id}}'>
                            @endif
                            <label class="col-sm-2 control-label pull-left">名稱</label>
                            <div class="col-sm-3">
                                @if( ($type ?? '') == 'edit')
                                    <input type='text' name='name' value='{{$role->name}}' required>
                                @else
                                    <input type='text' name='name' required>
                                @endif
                            </div>
                        </div>
                        <label class="col-sm-2 control-label pull-left">備註</label>

                        @if( ($type ?? '') == 'edit')
                            <input type='text' name='label' value='{{$role->label}}' required>
                        @else
                            <input type='text' name='label' required>
                        @endif
                    </div>
                    <simple-data-table-componet
                        :table_id='"permission_list"'
                        :table_head='"權限列表"'
                        :table_title='["選擇","名稱"]'
                        :type = '"select"'
                        @if( ($type ?? '') == 'edit')
                        :select_id='{{ json_encode( $role->permissions->pluck('id')) }}'
                        @endif
                        :row = '{!! json_encode($permissionList) !!}'
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

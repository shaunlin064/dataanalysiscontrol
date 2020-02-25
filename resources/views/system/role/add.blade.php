<?php
    /**
     * Created by PhpStorm.
     * User: shaun
     * Date: 2019/10/25
     * Time: 10:19 上午
     */
?>
@extends('layout')

@section('title','DAC | 新增群組權限')

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
                    <role-form
                        :table_id='"permissionsTable"'
                        :csrf= '"{{csrf_token()}}"'
                        :row = '{!! json_encode($permissionList) !!}'
                        :type='"{{$type ?? 'add'}}"'
                        @if( ($type ?? '') == 'edit')
                            :role='{!! json_encode($role) !!}'
                            :select_id='{{ json_encode( $role->permissions->pluck('id')) }}'
                        @endif
                    ></role-form>
{{--                    <simple-data-table-componet--}}
{{--                        :table_id='"permission_list"'--}}
{{--                        :table_head='"權限列表"'--}}
{{--                        :table_title='["選擇","名稱"]'--}}
{{--                        :type = '"select"'--}}
{{--                        @if( ($type ?? '') == 'edit')--}}
{{--                        :select_id='{{ json_encode( $role->role->pluck('id')) }}'--}}
{{--                        @endif--}}
{{--                        :row = '{!! json_encode($permissionList) !!}'--}}
{{--                        :csrf= '"{{csrf_token()}}"'--}}
{{--                        :columns = '{!!json_encode($columns)!!}'--}}
{{--                    ></simple-data-table-componet>--}}
                <!-- /.box-body -->
                <div class="box-footer">
                    <role-form-submit
                        :csrf= '"{{csrf_token()}}"'
                        :domid='"permissionsTable"'
                        :post_action_url='"/system/rolePost"'
                        :role_id='"{{$role->id ?? 0}}"'
                    ></role-form-submit>
{{--                    <button type="submit" class="btn btn-info pull-right">送出</button>--}}
                </div>
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

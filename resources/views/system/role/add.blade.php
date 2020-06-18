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
                        :dom_id='"permissions_table"'
                        :csrf= '"{{csrf_token()}}"'
                        :row = '{!! json_encode($permissionList) !!}'
                        :type='"{{$type ?? 'add'}}"'
                        @if( ($type ?? '') == 'edit')
                            :role='{!! json_encode($role) !!}'
                            :select_id='{{ json_encode( $role->permissions->pluck('id')) }}'
                        @endif
                    ></role-form>
                <!-- /.box-body -->
                <div class="box-footer">
                    <role-form-submit
                        :csrf= '"{{csrf_token()}}"'
                        :domid='"permissions_table"'
                        :post_action_url='"/system/rolePost"'
                        :role_id='"{{$role->id ?? 0}}"'
                    ></role-form-submit>
                    <button type="submit" class="btn btn-info pull-right">送出</button>
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

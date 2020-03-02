<?php
    /**
     * Created by PhpStorm.
     * User: shaun
     * Date: 2020/2/21
     * Time: 下午3:44
     */
?>

@extends('layout')

@section('title','DAC | 權限設定')

@section('css')
@endsection

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        權限設定
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">權限設定</li>
    </ol>
@endsection

@extends('headerbar')


<!-- Content Wrapper. Contains page content -->
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <permission-class-data-table
                :table_id='"permissionClassTable"'
                :csrf='"{{csrf_token()}}"'
                :row='{!! $permissionClassData !!}'
                :ajax_url='"permissionClassAjaxPost"'
            >
            </permission-class-data-table>
            <permission-data-table
            :table_id='"permissionListTable"'
            :csrf='"{{csrf_token()}}"'
            :row='{!! $row !!}'
            :permission_class='{{ $permissionClassData }}'
            >
            </permission-data-table>
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

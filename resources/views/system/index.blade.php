<?php
    /**
     * Created by PhpStorm.
     * User: shaun
     * Date: 2019/10/24
     * Time: 4:43 下午
     */
?>

@extends('layout')

@section('title','DAC | 系統首頁')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        系統首頁
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">系統首頁</li>
    </ol>
@endsection

@extends('headerbar')


<!-- Content Wrapper. Contains page content -->
@section('content')
    <div class="row">
        <div class="col-xs-12">

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

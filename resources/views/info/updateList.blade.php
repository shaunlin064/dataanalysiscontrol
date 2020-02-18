<?php
    /**
     * Created by PhpStorm.
     * User: shaun
     * Date: 2020/2/17
     * Time: 下午3:05
     */
?>
@extends('layout')

@section('title','DAC | title')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        系統更新
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">title</li>
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

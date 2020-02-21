<?php
    /**
     * Created by PhpStorm.
     * User: shaun
     * Date: 2020/2/12
     * Time: 下午3:35
     */
    ?>
@extends('layout')

@section('title','DAC | Menu設定')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        Menu設定
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Menu設定</li>
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
        <menu-list
            :csrf='"{{csrf_token()}}"'
            :row='{!! json_encode($listdata->toArray()) !!}'
        ></menu-list>
        <!-- /.col -->
    </div>
    <!-- /.row -->
@endsection

@section('script')

    <!-- page script -->
    <script>
        $(function () {

        });
    </script>
@endsection

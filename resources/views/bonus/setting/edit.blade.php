<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-18
	 * Time: 15:33
	 */
?>
@extends('layout')

@section('title','DAC | 責任額設定')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        責任額編輯
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">責任額設定編輯頁面</li>
    </ol>
@endsection

@extends('headerbar')

<!-- Content Wrapper. Contains page content -->
@section('content')
    <div class="row">
        <div class="col-md-4">
            <profile-component :user_data='{!! json_encode($userData) !!}'></profile-component>
        </div>
        <!-- /.col -->
        <div class="col-md-8">
            <bonus-group-component :arg='{type:"edit",csrf_token:"{{ csrf_token()}}",setting:@json($row),form_action:"{{Route('bonus.setting.update')}}",history:{{ json_encode($userBonusHistory) }}}'></bonus-group-component>
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





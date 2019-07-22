<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-19
	 * Time: 12:24
	 */
?>

@extends('layout')

@section('title','DAC | 責任額檢視')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        責任額檢視
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">責任額檢視頁面</li>
    </ol>
@endsection

@extends('headerbar')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <profile-component :user_data='{!! json_encode($userData) !!}'></profile-component>
        </div>
        <!-- /.col -->
        <div class="col-md-8">
            <bonus-group-component :arg='{type:"view",history:{{ json_encode($userBonusHistory) }}}'></bonus-group-component>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
@endsection

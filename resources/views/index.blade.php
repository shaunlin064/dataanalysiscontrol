<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2018/12/17
	 * Time: 3:57 PM
	 */

//	dd(session('userData')['user']['id']);
?>

@extends('layout')

@section('title','Jsadways | DAC')

{{--導航 麵包屑--}}
@section('content-header')
  <h1>
{{--    Dashboard--}}
{{--    <small>Version 2.0</small>A--}}
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
{{--    <li class="active">Dashboard</li>--}}
  </ol>
@endsection

@extends('headerbar')

{{--@section('sidebar')--}}
{{--	@extends('sidebar')--}}
{{--@endsection--}}
@section('content')
@endsection


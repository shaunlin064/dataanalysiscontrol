<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-08-01
	 * Time: 14:11
	 */
?>

@extends('layout')

@section('title','DAC | title name')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        獎金發放
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">name</li>
    </ol>
@endsection

@extends('headerbar')


<!-- Content Wrapper. Contains page content -->
@section('content')
    <div class="row" id='row1'>
        <div class="box box-widget widget-user">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-aqua">
                <div class='col-xs-6 col-md-6 pull-left'>
                    <h3 class="widget-user-username">{{$userData['name']}}</h3>
                    <h5 class="widget-user-desc">{{$userData['title']}}</h5>
                </div>
                <div class="form-group col-xs-6 col-md-6 pull-right">
                    <label>月份選擇</label>
                    <date-picker-component :dom_id='"review-datepicker"'></date-picker-component>
                    <!-- /.input group -->
                </div>
                                <a href="#" class="box-footer-info col-xs-12 pull-right">
                                    切換面板 <i class="fa fa-arrow-circle-right"></i>
                                    <i class="fa fa-refresh fa-spin"></i>
                                </a>
            </div>

            <div class="box-footer">
            </div>
        </div>
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

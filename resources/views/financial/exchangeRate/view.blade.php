<?php
    /**
     * Created by PhpStorm.
     * User: shaun
     * Date: 2019/11/13
     * Time: 下午1:40
     */

	$dateLastMonth = new DateTime();
	$dateLastMonth = $dateLastMonth->modify('-1Month')->format('Y/m');

?>

@extends('layout')

@section('title','DAC | 匯率檢視')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        匯率檢視
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">匯率檢視</li>
    </ol>
@endsection

@extends('headerbar')


<!-- Content Wrapper. Contains page content -->
@section('content')
    <div class="row" id='exchangeRateSetting'>
        <div class="col-xs-12">
            <div class="box">
                <simple-data-table-componet
                    :table_id='"exchangeTable"'
                    :table_head='"財報清單"'
                    :table_title='["月份","幣別","匯率"]'
                    :row = '{!! json_encode($row) !!}'
                    :columns = '{!!  json_encode(([['data'=>"set_date"],['data'=>"currency"],['data'=>"rate"]]))!!}'
                ></simple-data-table-componet>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
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


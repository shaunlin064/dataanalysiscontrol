<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-19
	 * Time: 12:24
	 */

?>

@extends('layout')

@section('title','DAC | 業績預估頁面')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        獎金頁面
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">獎金頁面</li>
    </ol>
@endsection

@extends('headerbar')

@extends('sidebar')

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
{{--                <a href="#" class="box-footer-info col-xs-12 pull-right">--}}
{{--                    切換面板 <i class="fa fa-arrow-circle-right"></i>--}}
{{--                    <i class="fa fa-refresh fa-spin"></i>--}}
{{--                </a>--}}
            </div>

            <div class="box-footer">

                <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-12 border-right">
                        <!-- small box -->
                        <box-info-component
                                :color='"bg-aqua"'
                                :icon='"fa fa-child"'
                                :vuex_field='"profit"'
                                :profit='{{ $boxData['profit'] }}'
                                :text='"預估獎金總計"'>
                        </box-info-component>
                    </div>
                    <!-- ./col -->
                    <div class="col-md-4 col-sm-6 col-xs-12 border-right">
                        <box-info-component
                                :color='"bg-green"'
                                :icon='"ion ion-stats-bars"'
                                :vuex_field='"bonus_rate"'
                                :bonus_rate='{{ $boxData['bonus_rate'] }}'
                                :text='"bonus Rate"'>
                        </box-info-component>
                    </div>
                    <!-- ./col -->
                    <div class="col-md-4 col-sm-6 col-xs-12 border-right">
                        <box-progress-component
                                :color='"bg-aqua"'
                                :icon='"fa fa-flag-o"'
                                :vuex_field='"bonus_next_level"'
                                :bonus_next_amount='{{ $boxData['bonus_next_amount'] }}'
                                :bonus_next_percentage='{{ $boxData['bonus_next_percentage'] }}'
                                :title='"下一階段獎金差額"'
                        ></box-progress-component>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-md-6 col-sm-12 col-xs-12 border-right">
                        <chart-component
                                :dom_id='"chart-pie"'
                                :type='"pie"'
                                :title='"金流狀態"'
                                :labels='["未收款","已收款","未發獎金","已發獎金"]'
                                :chart_data='{{ json_encode($chartData) }}'
                        ></chart-component>
                    </div>
                    <div class="col-md-6 col-sm-12 col-xs-12 border-right">
                        <!-- DONUT CHART -->
                        <chart-component
                                :dom_id='"chart-bar"'
                                :type='"bar"'
                                :title='"業績統計"'
                                :labels='[" "]'
                                :chart_data='{{ json_encode($chartDataBar) }}'
                        ></chart-component>
                        <!-- /.box -->
                    </div>
                    <!-- ./col -->
                </div>
            </div>
        </div>
        <!-- /.widget-user -->
        <div class="col-xs-12">
            <data-table-component :box_title='"案件清單"' :user_id='{{$userData['uId']}}' :data_table='{{ json_encode($dataTable) }}' :dom_id='"bonusTable"'></data-table-component>
        </div>
        <!-- /.box -->
        </div>
    </div>
    <!-- /.row -->
@endsection

@section('script')
    <!-- page script -->
    <script>
        $(function () {

            {{--var data1 = '{!! json_encode($dataTable['data']) !!}';--}}
            {{--    console.log(data1);--}}

        });
    </script>
@endsection

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
    <exchange-ajax :csrf='"{{csrf_token()}}"'></exchange-ajax>
    <div class="row" id='exchangeRateSetting'>
        <div class="col-xs-12">
            <div class="box box-widget widget-user" >
{{--                style='overflow: hidden;background-color: #00c0ef;'--}}
                <div class="widget-user-header bg-aqua">
                    <div class="small-box no-shadow col-md-4 col-sm-4 col-xs-12">
                        <div class="inner text-center" data-step="1" data-intro='業績系統計算匯率為使用當月最後一天即期賣出匯率'>
                            <div>
                                <h3 id='exchange_last'>0</h3>
                            </div>
                            <p>業績系統計算匯率</p>
                        </div>
                    </div>
                    <!-- small box -->
                    <div class="form-group col-md-4 col-sm-4 col-xs-12">
                        <label>幣別選擇:</label>
                        <select2 :id='"select_currency"'
                                 :multiple='false'
                                 :placeholder='"幣別選擇"'
                                 :row='{!! json_encode($currencys) !!}'
                        ></select2>
                        <load-item></load-item>
                    </div>
                    <div class="form-group col-md-4 col-sm-4 pull-left">
                        <label>Date range:</label>
                        <date-picker-component
                            :dom_id='"set_date"'
                            :date='"{{$dateLastMonth}}"'
                        ></date-picker-component>
                    </div>
                </div>
            </div>
            <exchange-chart-line
                :table_id='"chart_exchange_line_money"'
                :title='"現金匯率"'
                :csrf='"{{csrf_token()}}"'
                :item_labels='["本行現金買入","本行現金賣出"]'
                :labels='[]'
                :height='300'
                :chart_data='{{ json_encode([
                ["data"=>[0]],
                ["data"=>[0]]
                ]) }}'
            >
            </exchange-chart-line>
            <exchange-chart-line
                :table_id='"chart_exchange_line_period"'
                :title='"即期匯率"'
                :csrf='"{{csrf_token()}}"'
                :item_labels='["本行即期買入","本行即期賣出"]'
                :labels='[]'
                :height='300'
                :chart_data='{{ json_encode([
                ["data"=>[0]],
                ["data"=>[0]]
                ]) }}'
            >
            </exchange-chart-line>
            <div class="box">
                <simple-data-table-componet
                    :table_id='"exchange_rates_list"'
                    :table_head='"匯率"'
                    :table_title='["掛牌日期","現金買入","現金賣出","即期買入","即期賣出"]'
                    :row = '[]'
                    :ex_buttons='["excel"]'
                    :columns = '{{json_encode(([['data'=>"0"],['data'=>"1"],['data'=>"2"],['data'=>"3"],['data'=>"4"]]))}}'
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


<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-19
	 * Time: 12:24
	 */

?>

@extends('layout')

@section('title','DAC | 業績統計檢視')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        業績統計檢視
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">業績統計檢視</li>
    </ol>
@endsection

@extends('headerbar')

@section('content')
    <div class="row">
        <div class="box box-widget widget-user">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-aqua">
                <!-- small box -->
                <div class="small-box no-shadow col-md-6 col-sm-6 col-xs-12">
                    {{--<div class="inner text-center">--}}
                    {{--    <count-total :domid='"total_money"'></count-total>--}}
                    {{--    <p>總計發放金額</p>--}}
                    {{--</div>--}}
                </div>
                <div class="form-group col-xs-8 col-md-6 pull-left">
                    <date-range :start_date='"{{date('Y-m-01')}}"' :end_date='"{{date('Y-m-01')}}"'></date-range>
                </div>
            </div>
            <div class="box-footer">
                <div class="row">
                    <div class="col-lg-6">
                        <label>選擇成員</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name='selectType' data-type='select_user' @if(empty($saleGroups)){{'checked'}}@endif>
                            </span>
                            <select2 :id='"select_user"'
                                     :multiple='true'
                                     :disabled='{{empty($saleGroups) ? "false" : "true" }}'
                                     :placeholder='"選擇成員"'
                                     :row='{!! json_encode($userList) !!}'
                            ></select2>
                        </div>
                        <!-- /input-group -->
                    </div>
                    <!-- /.col-lg-6 -->
                    {{--                    @if($saleGroups)--}}
                    <div class="col-lg-6">
                        <label>選擇團隊</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name='selectType' data-type='select_groups' @if($saleGroups){{'checked'}}@else{{'disabled'}}@endif >
                            </span>
                            <select2 :id='"select_groups"'
                                     :multiple='true'
                                     :placeholder='"選擇團隊"'
                                     :disabled='{{$saleGroups ? "false" : "true" }}'
                                     :row='{!! json_encode($saleGroups) !!}'
                            ></select2>
                        </div>
                        <!-- /input-group -->
                    </div>
                    <!-- /.col-lg-6 -->
                    {{--                        @endif--}}
                </div>
            </div>
        </div>
        {{--<div class="box box-widget widget-user">--}}
            <!-- Add the bg color to the header using any of the bg-* classes -->
            {{--<div class="widget-user-header bg-aqua">--}}
            {{--    <div class='col-xs-6 col-md-6 pull-left'>--}}
            {{--        <h3 class="widget-user-username">{{$userData['name']}}</h3>--}}
            {{--        <h5 class="widget-user-desc">{{$userData['title']}}</h5>--}}
            {{--    </div>--}}
            {{--    <div class="form-group col-xs-6 col-md-6 pull-right">--}}
            {{--        <label>月份選擇</label>--}}
            {{--        <date-picker-component :dom_id='"review-datepicker"'></date-picker-component>--}}
            {{--        <!-- /.input group -->--}}
            {{--    </div>--}}
            {{--    <a href="#" class="box-footer-info col-xs-12 pull-right">--}}
            {{--        切換面板 <i class="fa fa-arrow-circle-right"></i>--}}
            {{--        <i class="fa fa-refresh fa-spin"></i>--}}
            {{--    </a>--}}
            {{--</div>--}}

                {{--<div class="row">--}}
                {{--    <div class="col-md-4 col-sm-6 col-xs-12 border-right">--}}
                {{--        <!-- small box -->--}}
                {{--        <box-info-component--}}
                {{--                :color='"bg-aqua"'--}}
                {{--                :icon='"fa fa-child"'--}}
                {{--                :vuex_field='"profit"'--}}
                {{--                :profit='{{ $boxData['profit'] }}'--}}
                {{--                :text='"預估獎金總計"'>--}}
                {{--        </box-info-component>--}}
                {{--    </div>--}}
                {{--    <!-- ./col -->--}}
                {{--    <div class="col-md-4 col-sm-6 col-xs-12 border-right">--}}
                {{--        <box-info-component--}}
                {{--                :color='"bg-green"'--}}
                {{--                :icon='"ion ion-stats-bars"'--}}
                {{--                :vuex_field='"bonus_rate"'--}}
                {{--                :bonus_rate='{{ $boxData['bonus_rate'] }}'--}}
                {{--                :bonus_direct='{{ $boxData['bonus_direct'] }}'--}}
                {{--                :text='"bonus Rate"'>--}}
                {{--        </box-info-component>--}}
                {{--    </div>--}}
                {{--    <!-- ./col -->--}}
                {{--    <div class="col-md-4 col-sm-6 col-xs-12 border-right">--}}
                {{--        <box-progress-component--}}
                {{--                :color='"bg-aqua"'--}}
                {{--                :icon='"fa fa-flag-o"'--}}
                {{--                :vuex_field='"bonus_next_level"'--}}
                {{--                :bonus_next_amount='{{ $boxData['bonus_next_amount'] }}'--}}
                {{--                :bonus_next_percentage='{{ $boxData['bonus_next_percentage'] }}'--}}
                {{--                :title='"下一階段獎金差額"'--}}
                {{--        ></box-progress-component>--}}
                {{--        <!-- /.info-box -->--}}
                {{--    </div>--}}
                {{--    <!-- /.col -->--}}
                {{--</div>--}}
                <!-- /.row -->
                    <div class="col-md-6 col-sm-12 col-xs-12 border-right">
                        <!-- Pie CHART -->
                        <chart-component
                                :table_id='"chart_money_status"'
                                :type='"pie"'
                                :title='"金流狀態"'
                                :labels='["未收款","已收款"]'
                                :ajax_url= '"/bonus/review/getAjaxData"'
                                :csrf= '"{{csrf_token()}}"'
                                :chart_data='{{ json_encode($chartData) }}'
                        ></chart-component>
                    </div>
                    <div class="col-md-6 col-sm-12 col-xs-12 border-right">
                        <!-- DONUT CHART -->
                        <chart-component
                                :table_id='"chart_financial_bar"'
                                :type='"bar"'
                                :title='"業績統計"'
                                :ajax_url= '"/bonus/review/getAjaxData"'
                                :csrf= '"{{csrf_token()}}"'
                                :labels='{{json_encode($chartDataBarLabels)}}'
                                :chart_data='{{ json_encode($chartDataBar) }}'
                        ></chart-component>
                        <!-- /.box -->
                    </div>
                    <!-- ./col -->
                {{--</div>--}}
            <div class="col-xs-12 col-md-6">
                <simple-data-table-componet
                        :table_id='"progress_list"'
                        :table_head='"成員績效表"'
                        :table_title='["月份","業務","團隊","毛利","達成率","%","獎金","英雄榜"]'
                        :row = '{!! json_encode($progressDatas) !!}'
                        {{--:ex_buttons= '["excel"]'--}}
                        :csrf= '"{{csrf_token()}}"'
                        :columns = '{!!json_encode($progressColumns)!!}'
                        :ajax_url= '"/bonus/review/getAjaxData"'
                        :page_length='10'
                        :length_change='"hide"'
                ></simple-data-table-componet>
            </div>
            <div class="col-xs-12 col-md-6">
                <simple-data-table-componet
                        :table_id='"group_progress_list"'
                        :table_head='"團隊績效表"'
                        :table_title='["月份","團隊","毛利","達成率"]'
                        :row = '{!! json_encode($groupProgressDatas) !!}'
                        {{--:ex_buttons= '["excel"]'--}}
                        :csrf= '"{{csrf_token()}}"'
                        :columns = '{!!json_encode($groupProgressColumns)!!}'
                        :ajax_url= '"/bonus/review/getAjaxData"'
                        :page_length='10'
                        :length_change='"hide"'
                ></simple-data-table-componet>
            </div>
            <div class="col-xs-12">
                <simple-data-table-componet
                        :table_id='"bonus_list"'
                        :table_head='"財報清單"'
                        :table_title='["月份","業務","團隊","案件","媒體","類型","幣別","公司","收入","成本","毛利","收款狀態","發放狀態"]'
                        :row = '{!! json_encode($bonusData) !!}'
                        :ex_buttons= '["excel"]'
                        :csrf= '"{{csrf_token()}}"'
                        :columns = '{!!json_encode($bonusColumns)!!}'
                        :ajax_url= '"/bonus/review/getAjaxData"'
                ></simple-data-table-componet>
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

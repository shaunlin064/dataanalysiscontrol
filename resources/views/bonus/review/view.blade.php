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
                <div class="form-group col-md-6 col-sm-12 col-xs-12">
                    <label>快速查詢:</label>
                   <select-button-group></select-button-group>
                </div>
                <div class="form-group col-xs-8 col-md-6 pull-left">
                    <date-range :input_start_date='"{{date('Y-m-01')}}"' :input_end_date='"{{date('Y-m-01')}}"'></date-range>
                </div>
            </div>
            <div class="box-footer">
                <div class="row">
                    <div class="col-lg-6">
                        <label>選擇成員</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name='selectType'
                                     data-type='select_user' @if(empty($saleGroups)){{'checked'}}@endif>
                            </span>
                            <select2 :id='"select_user"'
                                     :multiple='true'
                                     :selected='true'
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
                              <input type="radio" name='selectType'
                                     data-type='select_groups' @if($saleGroups){{'checked'}}@else{{'disabled'}}@endif >
                            </span>
                            <select2 :id='"select_groups"'
                                     :multiple='true'
                                     :placeholder='"選擇團隊"'
                                     :selected='true'
                                     :disabled='{{$saleGroups ? "false" : "true" }}'
                                     :row='{!! json_encode($saleGroups) !!}'
                            ></select2>
                        </div>
                        <!-- /input-group -->
                    </div>
                    <!-- /.col-lg-6 -->
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">資料篩選</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <form class="form-horizontal box-body">
                    <div class="form-group col-sm-6">
                        <label class="col-sm-4 control-label input-md">代理商 : </label>
                        <div class="col-sm-8">
                            <select2-customer :id='"agency_ids"'
                                              :selected='false'
                                              :multiple='true'
                                              :placeholder='"請選擇"'
                                              :row='{!! json_encode($agencyList) !!}'
                            ></select2-customer>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="col-sm-4 control-label input-md">直客 : </label>
                        <div class="col-sm-8">
                            <select2-customer :id='"client_ids"'
                                              :selected='false'
                                              :multiple='true'
                                              :placeholder='"請選擇"'
                                              :row='{!! json_encode($clientList) !!}'
                            ></select2-customer>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="col-sm-4 control-label input-md">媒體 : </label>
                        <div class="col-sm-8">
                            <select2-customer :id='"medias_names"'
                                              :selected='false'
                                              :multiple='true'
                                              :placeholder='"請選擇"'
                                              :row='{!! json_encode($medias) !!}'
                            ></select2-customer>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="col-sm-4 control-label input-md">媒體代理商 : </label>
                        <div class="col-sm-8">
                            <select2-customer :id='"media_companies_ids"'
                                              :selected='false'
                                              :multiple='true'
                                              :placeholder='"請選擇"'
                                              :row='{!! json_encode($mediaCompaniesList) !!}'
                            ></select2-customer>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class='col-md-6 col-sm-12 col-xs-12 border-right'>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">客戶列表</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <div class="box-body">
                    <div class='owl-carousel owl-theme'>
                        <chart-customer-precentage-profit
                            :table_id='"customer_precentage_profit"'
                            :title='"總毛利佔比"'
                            :labels='["代理商","直客","AP","BR","EC"]'
                        ></chart-customer-precentage-profit>
                        <chart-customer-profit-bar
                            :table_id='"customer_profit_bar"'
                            :title='"客戶毛利統計"'
                            :labels='["代理商","直客","毛利"]'
                        ></chart-customer-profit-bar>
                    </div>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">客戶列表</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <div class="box-body">
                    <div class='owl-carousel owl-theme'>
                        <div class="col-xs-12">
                            <simple-data-table-componet
                                :table_id='"customer_profit_data"'
                                :table_head='"客戶列表"'
                                :table_title='["名稱","類型","發稿量","毛利"]'
                                :row='[]'
                                {{--                        :ex_buttons= '["excel"]'--}}
                                :csrf='"{{csrf_token()}}"'
                                :columns='{!!json_encode($customerProfitColumns)!!}'
                                :ajax_url='"/bonus/review/getAjaxData"'
                                :page_length='15'
                                :length_change='"hide"'
                            ></simple-data-table-componet>
                        </div>
                        <div class="col-xs-12">
                            <simple-data-table-componet
                                :table_id='"media_companies_profit_data"'
                                :table_head='"媒體代理商列表"'
                                :table_title='["名稱","毛利"]'
                                :row='[]'
                                {{--                        :ex_buttons= '["excel"]'--}}
                                :csrf='"{{csrf_token()}}"'
                                :columns='{!!json_encode($mediaCompaniesProfitColumns)!!}'
                                :ajax_url='"/bonus/review/getAjaxData"'
                                :page_length='15'
                                :length_change='"hide"'
                            ></simple-data-table-componet>
                        </div>
                        <div class="col-xs-12">
                            <simple-data-table-componet
                                :table_id='"medias_profit_data"'
                                :table_head='"媒體列表"'
                                :table_title='["名稱","銷售區域","毛利"]'
                                :row='[]'
                                {{--                        :ex_buttons= '["excel"]'--}}
                                :csrf='"{{csrf_token()}}"'
                                :columns='{!!json_encode($mediasProfitColumns)!!}'
                                :ajax_url='"/bonus/review/getAjaxData"'
                                :page_length='15'
                                :length_change='"hide"'
                            ></simple-data-table-componet>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class='col-md-6 col-sm-12 col-xs-12 border-right'>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">業績列表</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <div class="box-body">
                    <div class='owl-carousel owl-theme'>
                        <chart-component
                            :table_id='"chart_financial_bar"'
                            :type='"bar"'
                            :title='"業績統計"'
                            :ajax_url='"/bonus/review/getAjaxData"'
                            :csrf='"{{csrf_token()}}"'
                            :labels='{{json_encode($chartDataBarLabels)}}'
                            :chart_data='{{ json_encode($chartDataBar) }}'
                        ></chart-component>
                        <chart-component
                            :table_id='"chart_money_status"'
                            :type='"pie"'
                            :title='"金流狀態"'
                            :labels='["未收款","已收款"]'
                            :ajax_url='"/bonus/review/getAjaxData"'
                            :csrf='"{{csrf_token()}}"'
                            :chart_data='{{ json_encode($chartData) }}'
                        ></chart-component>
                    </div>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">業績圖表</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <div class="box-body">
                    <div class='owl-carousel owl-theme'>
                        <div class="col-xs-12">
                            <simple-data-table-componet
                                :table_id='"progress_list"'
                                :table_head='"成員績效表"'
                                :table_title='["月份","業務","團隊","毛利","達成率","%","獎金","英雄榜"]'
                                :row='{!! json_encode($progressDatas) !!}'
                                {{--:ex_buttons= '["excel"]'--}}
                                :csrf='"{{csrf_token()}}"'
                                :columns='{!!json_encode($progressColumns)!!}'
                                :ajax_url='"/bonus/review/getAjaxData"'
                                :page_length='10'
                                :length_change='"hide"'
                            ></simple-data-table-componet>
                        </div>
                        <div class="col-xs-12">
                            <simple-data-table-componet
                                :table_id='"group_progress_list"'
                                :table_head='"團隊績效表"'
                                :table_title='["月份","團隊","毛利","達成率"]'
                                :row='{!! json_encode($groupProgressDatas) !!}'
                                {{--:ex_buttons= '["excel"]'--}}
                                :csrf='"{{csrf_token()}}"'
                                :columns='{!!json_encode($groupProgressColumns)!!}'
                                :ajax_url='"/bonus/review/getAjaxData"'
                                :page_length='15'
                                :length_change='"hide"'
                            ></simple-data-table-componet>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class='col-xs-12 content-center'>
            <chart-all-year-profit-line
                :table_id='"customer_profit_bar1"'
                :title='"年度統計"'
                :datas='{!! json_encode($allYearProfit) !!}'
            ></chart-all-year-profit-line>
        </div>
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">業績列表</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <div class="box-body">
                    <simple-data-table-componet
                        :table_id='"bonus_list"'
                        :table_head='"財報清單"'
                        :table_title='["月份","業務","團隊","案件","媒體","類型","幣別","公司","收入","成本","毛利","收款狀態","發放狀態"]'
                        :row='{!! json_encode($bonusData) !!}'
                        :ex_buttons='["excel"]'
                        :csrf='"{{csrf_token()}}"'
                        :columns='{!!json_encode($bonusColumns)!!}'
                        :ajax_url='"/bonus/review/getAjaxData"'
                    ></simple-data-table-componet>
                </div>
            </div>
        </div>
        <!-- /.box -->
    </div>

    </div>
    <!-- /.row -->
@endsection

@section('script')
    <!-- page script -->
    <script type="text/javascript">
        $(document).ready(function () {

            $('.owl-carousel').owlCarousel({
                loop: false,
                center: true,
                items: 1,
                margin: 10,
                responsive: {
                    800: {
                        items: 1
                    }
                }
            })
        });
    </script>
@endsection

<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-09-03
	 * Time: 14:42
	 */
?>

@extends('layout')

@section('title','DAC | 獎金發放檢視')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        獎金發放檢視
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">獎金發放檢視</li>
    </ol>
@endsection

@extends('headerbar')


<!-- Content Wrapper. Contains page content -->
@section('content')
    <financial-provide-ajax-component :csrf='"{{csrf_token()}}"'></financial-provide-ajax-component>
    <div class='row'>
        <div class="box box-widget widget-user">
            <div class="widget-user-header bg-aqua">

                <div class="small-box no-shadow col-md-4 col-sm-4 col-xs-12">
                    <div class="inner text-center">
                        <count-total :domid='"total_money"' data-step="4" data-intro="該條件發放總金額..<a href='/info/scheduleList'>獎金發放規則請見</a>"></count-total>
                        <p>總計發放金額</p>
                    </div>
                </div>
                <div class="form-group col-md-4 col-sm-4 col-xs-12">
                    <label>快速查詢:</label>
                    <select-button-group></select-button-group>
                </div>
                <div class="form-group col-xs-12 col-md-4 pull-left">
                    <date-range :dom_id='"provide_date_ranger"' :input_start_date='"{{date("Y-m-01")}}"' :input_end_date='"{{date("Y-m-01")}}"'
                                data-step="1" data-intro="選擇要檢視的月份區間"></date-range>
                </div>
            </div>
            <div class="box-footer">
                <div class="row">
                    <div class="col-lg-6">
                        <label>選擇成員</label>
                        <div class="input-group" data-step="2" data-intro="選擇成員">
                            <span class="input-group-addon">
                              <input type="radio" name='selectType' data-type='select_user' @if(empty($saleGroups)){{'checked'}}@endif>
                            </span>
                            <select2 :id='"select_user"'
                                     :multiple='"multiple"'
                                     :disabled='{{empty($saleGroups) ? "false" : "true" }}'
                                     :placeholder='"選擇成員"'
                                     :row='{!! json_encode($userList) !!}'
                            ></select2>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>選擇團隊</label>
                        <div class="input-group" data-step="3" data-intro="選擇團隊">
                            <span class="input-group-addon">
                              <input type="radio" name='selectType' data-type='select_groups' @if($saleGroups){{'checked'}}@else{{'disabled'}}@endif >
                            </span>
                            <select2 :id='"select_groups"'
                                     :multiple='"multiple"'
                                     :placeholder='"選擇團隊"'
                                     :disabled='{{$saleGroups ? "false" : "true" }}'
                                     :row='{!! json_encode($saleGroups) !!}'
                            ></select2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class='row'><div class='col-md-12 col-sm-12 col-xs-12 border-right'>
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">個人統計</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-ui-contorl='statistics_char_person'><i class="fa fa-minus"></i>
                            </button>
                        </div>
                        <!-- /.box-tools -->
                    </div>
                    <div class="box-body">
                        <chart-component
                            :table_id='"chart_provide_bar"'
                            :type='"bar"'
                            :title='"個人"'
                            :labels='[]'
                            :height='300'
                            :chart_data='{{ json_encode([ ["data"=>[0,0]] ]) }}'
                        ></chart-component>
                    </div>
                </div>
            </div></div>
        <div class='row'><div class='col-md-12 col-sm-12 col-xs-12 border-right'>
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">月份統計</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-ui-contorl='statistics_char_stack'><i class="fa fa-minus"></i>
                            </button>
                        </div>
                        <!-- /.box-tools -->
                    </div>
                    <div class="box-body">
                        <provide-chart-stack
                            :table_id='"chart_stack_bar"'
                            :title='"月份"'
                            :height='500'>
                        </provide-chart-stack>
                    </div>
                </div>
            </div></div>
        <div class='row'><div class='col-md-12 col-sm-12 col-xs-12 border-right'>
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">團隊統計</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-ui-contorl='statistics_char_group'><i class="fa fa-minus"></i>
                            </button>
                        </div>
                        <!-- /.box-tools -->
                    </div>
                    <div class="box-body">
                        <chart-component
                            :table_id='"chart_provide_pie"'
                            :type='"pie"'
                            :title='"團隊"'
                            :labels='[]'
{{--                            :height='300'--}}
                            :chart_data='{{ json_encode([ ["data"=>[0]],["data"=>[0] ]  ]) }}'
                        ></chart-component>
                    </div>
                </div>
            </div></div>
        @if($saleGroups)
            <simple-data-table-componet
                    :table_id='"provide_groups_list"'
                    :table_head='"招集人-團隊獎金"'
                    :table_title='["發放月份","月份","業務","團隊","團隊毛利","比例","獎金"]'
                    :row = '{{ json_encode($saleGroupsReach) }}'
                    :ex_buttons= '["excel"]'
                    :csrf= '"{{csrf_token()}}"'
                    :columns = '{!!json_encode($saleGroupsReachColumns)!!}'
                    :ajax_url= '"/financial/provide/getAjaxProvideData"'
            ></simple-data-table-componet>
            <simple-data-table-componet
                    :table_id='"provide_bonus_beyond_list"'
                    :table_head='"招集人-領導獎金"'
                    :table_title='["發放月份","月份","業務","團隊","獎金"]'
                    :row = '{{ "[]" }}'
                    :ex_buttons= '["excel"]'
                    :csrf= '"{{csrf_token()}}"'
                    :columns = '{!!json_encode($bonusBeyondColumns)!!}'
                    :ajax_url= '"/financial/provide/getAjaxProvideData"'
            ></simple-data-table-componet>
        @endif
        <simple-data-table-componet
                :table_id='"provide_bonus_list"'
                :table_head='"獎金清單檢視"'
                :table_title='["發放月份","月份","業務","團隊","案件","媒體","類型","毛利","比例","獎金"]'
                :row = '{{ json_encode($provideBonus) }}'
                :ex_buttons= '["excel"]'
                :csrf= '"{{csrf_token()}}"'
                :columns = '{!!json_encode($provideBonusColumns)!!}'
                :ajax_url = '"/financial/provide/getAjaxProvideData"'
        ></simple-data-table-componet>
    </div>

    <!-- /.row -->
@endsection

@section('script')

    <!-- page script -->
    <script>
        $(document).ready(function () {
            if(cookie.provide_view === undefined){
                cookie.provide_view = {};
            }
            let boxToggleCookie = cookie.provide_view;
            $('button[data-ui-contorl]').click(function(){
                let field = $(this).data('ui-contorl');
                eval(`boxToggleCookie.${field} = $(this).children('i').hasClass('fa-plus') ? 1 : 0;`);
                setCookie('ui-contorl',JSON.stringify(cookie));
            });

            if(boxToggleCookie !== undefined){
                Object.keys(boxToggleCookie).forEach(key=>{
                    if(boxToggleCookie[key] === 0){
                        $('*[data-ui-contorl="'+key+'"]').parents('.box-header.with-border').parent().addClass('collapsed-box');
                        $('*[data-ui-contorl="'+key+'"]').children('i.fa').removeClass('fa-minus').addClass('fa-plus');
                    }
                });
            }
        });
    </script>
@endsection

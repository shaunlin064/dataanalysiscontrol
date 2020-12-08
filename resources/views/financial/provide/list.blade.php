<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-08-01
	 * Time: 14:11
	 */

?>

@extends('layout')

@section('title','DAC | 獎金發放')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        獎金發放
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">獎金發放</li>
    </ol>
@endsection

@extends('headerbar')


<!-- Content Wrapper. Contains page content -->
@section('content')
    <div class='row'>
        <div class="box box-widget widget-user">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-aqua">
                <!-- small box -->
                <div class="small-box no-shadow col-xs-12">
                    <div class="inner text-center" data-step="1" data-intro="此次已勾選發放獎金總金額">
                        <count-total :domid='"total_money"'></count-total>
                        <p>總計發放金額</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class='row'>
        <div class='col-md-12 col-sm-12 col-xs-12 border-right'>
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
                        :title='"個人統計"'
                        :labels='[]'
                        :height='300'
                        :chart_data='{{ json_encode([ ["data"=>0] ]) }}'
                    ></chart-component>
                </div>
            </div>
        </div>
    </div>
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
                        :height='500'
                        >
                    </provide-chart-stack>
                </div>
            </div>
        </div></div>
    <div class='row'>
        <div class='col-md-12 col-sm-12 col-xs-12 border-right'>
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">發放統計</h3>
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
{{--                        :height='300'--}}
                        :chart_data='{{ json_encode([ ["data"=>[0]],["data"=>[0] ]  ]) }}'
                    ></chart-component>
                </div>
            </div>
        </div>
    </div>

    <div class='row'>
        <div class='col-md-12'>
        <simple-data-table-componet
                :table_id='"provide_groups_list"'
                :table_head='"招集人-團隊獎金"'
                :table_title='["","月份","業務","團隊名稱","團隊毛利","比例","獎金"]'
                :row = '{{ json_encode($saleGroupsReach) }}'
                :columns = '{!!json_encode($saleGroupsTableColumns)!!}'
                :type = '"select"'
                :all_user_name='{{json_encode($allUserName)}}'
                data-step="2" data-intro="召集人獎金 每月16號後計算上月獎金..<a href='/info/scheduleList'>可參考系統排程表</a>"
        ></simple-data-table-componet>
        </div>
    </div>
    <div class='row'>
        <div class='col-md-12'>
            <simple-data-table-componet
                    :table_id='"provide_bonus_beyond_list"'
                    :table_head='"招集人-領導獎金"'
                    :table_title='["","月份","業務","團隊名稱","獎金"]'
                    :row = '{{ json_encode($bonusBeyondList) }}'
                    :columns = '{!!json_encode($bonusBeyondColumns)!!}'
                    :type = '"select"'
                    :all_user_name='{{json_encode($allUserName)}}'
            ></simple-data-table-componet>
        </div>
    </div>

{{--    </div>--}}
    {{--list --}}
    <div class='row'>
        <div class='col-md-12' >
            <p data-step="3" data-intro="個人獎金 須留意獎金加總需要為正數才能發放"></p>
            <simple-data-table-componet
                    :table_id='"provide_bonus_list"'
                    :table_head='"個人獎金"'
                    :table_title='["","月份","業務","團隊名稱","案件名稱","媒體","類型","毛利","比例","獎金"]'
                    :row = '{{ json_encode($bonuslist) }}'
                    :columns = '{!!json_encode($bonuslistColumns)!!}'
                    :type = '"select"'
                    :select_id='{!! json_encode($autoSelectIds) !!}'
                    :total_money='{{$total_mondey}}'
                    :csrf= '"{{csrf_token()}}"'
                    :page_length='99999'
                    :length_change='"hide"'
                    :all_user_name='{{json_encode($allUserName)}}'
            ></simple-data-table-componet>
            <provide-submit
                    :csrf= '"{{csrf_token()}}"'
                    :domid='"provide_submit"'
                    :post_action_url='"post"'
                    data-step="4" data-intro="最後記得要送出" ></provide-submit>
        </div>
        <!-- Colored raised button -->
    </div>

@endsection

@section('script')

    <!-- page script -->
    <script>
        $(document).ready(function () {
            if(cookie.provide_list === undefined){
                cookie.provide_list = {};
            }
            let boxToggleCookie = cookie.provide_list;
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

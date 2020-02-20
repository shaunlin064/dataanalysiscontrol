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
                {{--<div class="form-group col-xs-8 col-md-5 pull-left">--}}
                {{--    <label>月份選擇</label>--}}
                {{--    <date-picker-component :dom_id='"review-datepicker"'></date-picker-component>--}}
                {{--    <!-- /.input group -->--}}
                {{--</div>--}}
                {{--<div class='col-xs-3 col-md-2 pull-right'>--}}
                {{--    <label></label>--}}
                {{--    <div class='input-group'>--}}
                {{--        <a class="btn btn-app">--}}
                {{--            <i class="fa fa-save"></i>excel匯出--}}
                {{--        </a>--}}
                {{--    </div>--}}
                {{--</div>--}}
            </div>
        </div>

    </div>
    <div class='row'>
        <div class='col-md-12'>
        <simple-data-table-componet
                :table_id='"provide_sale_groups_bonus"'
                :table_head='"招集人獎金"'
                :table_title='["","月份","業務","團隊名稱","團隊毛利","比例","獎金"]'
                :row = '{{ json_encode($saleGroupsReach) }}'
                :columns = '{!!json_encode($saleGroupsTableColumns)!!}'
                :type = '"select"'
                data-step="2" data-intro="召集人獎金須自行手動勾選,每月16號後計算上月獎金..<a href='/info/scheduleList'>可參考系統排程表</a>"
        ></simple-data-table-componet>
        </div>
    </div>

{{--    </div>--}}
    {{--list --}}
    <div class='row'>
        <div class='col-md-12' >
            <p data-step="3" data-intro="個人獎金 每次系統都會自動勾選可發獎金人員,條件為目前所有該員獎金加總會正數,注意搜索並不會取消已勾選人員"></p>
            <simple-data-table-componet
                    :table_id='"provide_bonus"'
                    :table_head='"個人獎金"'
                    :table_title='["","月份","業務","團隊名稱","案件名稱","媒體","類型","毛利","比例","獎金"]'
                    :row = '{{ json_encode($bonuslist) }}'
                    :columns = '{!!json_encode($bonuslistColumns)!!}'
                    :type = '"select"'
                    :select_id='{!! json_encode($autoSelectIds) !!}'
                    :total_money='{{$total_mondey}}'
                    :csrf= '"{{csrf_token()}}"'
            ></simple-data-table-componet>
            <provide-submit
                    :csrf= '"{{csrf_token()}}"'
                    :domid='"provide_submit"'
                    :post_action_url='"post"'
                    data-step="4" data-intro="最後記得要送出" ></provide-submit>
            {{--<provide-data-table-component :arg='{--}}
            {{--"csrf_token" : "{{csrf_token()}}",--}}
            {{--"paginate_count": {{$paginate->count()}},--}}
            {{--"paginate" : {--}}
            {{--    "hasPages" : "{{$paginate->hasPages()}}",--}}
            {{--    "onFirstPage" : "{{$paginate->onFirstPage()}}",--}}
            {{--    "previousPageUrl" : "{{$paginate->previousPageUrl()}}",--}}
            {{--    "currentPage" : "{{$paginate->currentPage()}}",--}}
            {{--    "hasMorePages" : "{{$paginate->hasMorePages()}}",--}}
            {{--    "nextPageUrl" : "{{$paginate->nextPageUrl()}}",--}}
            {{--    "element" : {{ json_encode($paginateElement) }}--}}
            {{--},--}}
            {{--"sort" : "{{$sort}}",--}}
            {{--"sort_by" : "{{$sort_by}}",--}}
            {{--"show_item" : "{{$paginate->perPage()}}",--}}
            {{--"search_str" : "{{$search_str}}",--}}
            {{--"first_item_num" : {{$paginate->firstItem() ?? 0}},--}}
            {{--"last_item_num" : {{$paginate->lastItem() ?? 0 }},--}}
            {{--"total_item_num" : {{$paginate->total() ?? 0}},--}}
            {{--"row" : {{ json_encode($row)}},--}}
            {{--"users" : {{ json_encode(session('users'))}},--}}
            {{--"all_ids" : {!!  json_encode($allId)  !!},--}}
            {{--"select_ids" : {!! json_encode($selectIds) !!},--}}
            {{--"original_select_financial_id" : {!! json_encode($selectIds) !!},--}}
            {{--"total_alreday_select_money" : {{ $totalAlredaySelectMoney }}--}}
            {{--        }'></provide-data-table-component>--}}

        </div>
        <!-- Colored raised button -->
    </div>

@endsection

@section('script')

    <!-- page script -->
    <script>
        $(document).ready(function () {
        });
    </script>
@endsection

<?php
    /**
     * Created by PhpStorm.
     * User: shaun
     * Date: 2019-06-19
     * Time: 12:24
     */


    //	width: 100px;
    //    display: inline-block;
    //    white-space: nowrap;
    //    overflow: hidden;
    //    text-overflow: ellipsis;
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
    <bonus-review-ajax :csrf='"{{csrf_token()}}"'></bonus-review-ajax>
    <div class="row">
        <customer-modal></customer-modal>
        <div class="box box-widget widget-user">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-aqua">
                <!-- small box -->
                <div class="form-group col-md-6 col-sm-6 col-xs-12" data-step="1" data-intro="快速選擇月份區間">
                    <label>快速查詢:</label>
                    <select-button-group></select-button-group>
                    <load-item></load-item>
                </div>
                <div class="form-group col-md-6 col-sm-6 pull-left" data-step="2" data-intro="條件1:自訂時間">
                    <date-range :dom_id='"review_date_ranger"' :input_start_date='"{{date('Y-m-01')}}"'
                                :input_end_date='"{{date('Y-m-01')}}"'></date-range>
                </div>
            </div>

            <div class="box-footer" data-step="3" data-intro="條件2:人員團隊選擇">
                <div class="row">
                    <div class="col-lg-6">
                        <label>選擇成員</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name='selectType'
                                     data-type='select_user' @if(empty($saleGroups)){{'checked'}}@endif>
                            </span>
                            <select2 :id='"select_user"'
                                     :multiple='"multiple"'
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
                                     :multiple='"multiple"'
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
            <div class="box box-warning" data-step="4" data-intro="條件3:客戶媒體類型選擇">
                <div class="box-header with-border">
                    <h3 class="box-title">資料篩選</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"
                                data-ui-contorl='data_filter'><i
                                class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <form class="form-horizontal box-body">
                    <div class="form-group col-sm-6">
                        <label class="col-sm-4 control-label input-md">代理商 : </label>
                        <div class="col-sm-8">
                            <select2-customer :dom_id='"agency_ids"'
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
                            <select2-customer :dom_id='"client_ids"'
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
                            <select2-customer :dom_id='"medias_names"'
                                              :selected='false'
                                              :multiple='true'
                                              :placeholder='"請選擇"'
                                              :row='{!! json_encode($medias) !!}'
                            ></select2-customer>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="col-sm-4 control-label input-md">媒體經銷商 : </label>
                        <div class="col-sm-8">
                            <select2-customer :dom_id='"media_companies_ids"'
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
        <div class='col-sm-12 col-xs-12 border-right'>
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">比較圖表</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"
                                data-ui-contorl='compare_char'><i
                                class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <div class="box-body">
                    <chart-component
                        :table_id='"chart_financial_bar2"'
                        :type='"bar"'
                        :title='"選擇年度"'
                        :ajax_url='"/bonus/review/getAjaxData"'
                        :csrf='"{{csrf_token()}}"'
                        :labels='[]'
                        :height='300'
                        :chart_data='{{ json_encode([ ["data"=>0],["data"=>0],["data"=>0,"type"=> 'bar'] ]) }}'
                    ></chart-component>
                    <chart-component
                        :table_id='"chart_financial_bar_last_record"'
                        :type='"bar"'
                        :title='"對比年度"'
                        :ajax_url='"/bonus/review/getAjaxData"'
                        :csrf='"{{csrf_token()}}"'
                        :labels='[]'
                        :height='300'
                        :chart_data='{{ json_encode([ ["data"=>0],["data"=>0],["data"=>0,"type"=> 'bar'] ]) }}'
                    ></chart-component>
                    <bonus-chart-stack
                        :table_id='"chart_stack_bar"'
                        :title='"月份"'
                        :height='300'>
                    </bonus-chart-stack>
                </div>
            </div>
        </div>
        <div class='col-sm-12 col-xs-12 border-right'>
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">媒體圖表</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"
                                data-ui-contorl='customer_char'><i
                                class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <div class="box-body" data-step="8" data-intro="圓餅圖表可點擊查看細節">
                        <chart-media-precentage-profit
                            :table_id='"media_precentage_profit"'
                            :title='"媒體毛利佔比"'
                            :labels='[]'
                        ></chart-media-precentage-profit>
                </div>
            </div>
        </div>
        <div class='col-md-6 col-sm-12 col-xs-12 border-right'>
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">客戶圖表</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"
                                data-ui-contorl='customer_char'><i
                                class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <div class="box-body">
                    <div class='owl-carousel owl-theme' data-owl-name='customer_char'>
                        <chart-customer-profit-bar
                            :table_id='"customer_profit_bar"'
                            :title='"客戶毛利統計"'
                            :labels='["代理商","直客","毛利"]'
                        ></chart-customer-profit-bar>
                    </div>
                </div>
            </div>
            <div class="box box-warning">
                <div class="box-header with-border" data-step="6" data-intro="1.客戶列表 名稱可以點擊檢視更多資訊． 2.媒體經銷商列表 如有名稱為空白,代表後台案件媒體審核沒有設定媒體公司">
                    <h3 class="box-title">客戶列表</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"
                                data-ui-contorl='customer_list' data-step="7" data-intro="區塊圖表可以關閉收起"><i
                                class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <div class="box-body" data-step="5" data-intro="圖表可以左右切換">
                    <div class='owl-carousel owl-theme' data-owl-name='customer_list'>
                        <div class="col-xs-12">
                            <simple-data-table-componet
                                :table_id='"customer_profit_data"'
                                :table_head='"客戶列表"'
                                :table_title='["名稱","類型","發稿量","收入","成本","毛利","毛利率"]'
                                :row='[]'
                                :ex_buttons= '["excel"]'
                                :csrf='"{{csrf_token()}}"'
                                :columns='{!!json_encode($customerProfitColumns)!!}'
                                :ajax_url='"/bonus/review/getAjaxData"'
                                :page_length='15'
                                :length_change='"hide"'
                            ></simple-data-table-componet>
                        </div>
                        <div class="col-xs-12">
                            <simple-data-table-componet
                                :table_id='"customer_groups_profit_data"'
                                :table_head='"集團列表"'
                                :table_title='["名稱","發稿量","收入","成本","毛利","毛利率"]'
                                :row='[]'
                                :ex_buttons= '["excel"]'
                                :csrf='"{{csrf_token()}}"'
                                :columns='{!!json_encode($customerGroupProfitColumns)!!}'
                                :ajax_url='"/bonus/review/getAjaxData"'
                                :page_length='15'
                                :length_change='"hide"'
                            ></simple-data-table-componet>
                        </div>
                        <div class="col-xs-12">
                            <simple-data-table-componet
                                :table_id='"media_companies_profit_data"'
                                :table_head='"媒體經銷商列表"'
                                :table_title='["名稱","收入","成本","毛利","毛利率"]'
                                :row='[]'
                                :ex_buttons= '["excel"]'
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
                                :table_title='["名稱","銷售區域","收入","成本","毛利","毛利率"]'
                                :row='[]'
                                :ex_buttons= '["excel"]'
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
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">業績圖表</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"
                                data-ui-contorl='sale_char'><i
                                class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <div class="box-body">
                    <div class='owl-carousel owl-theme' data-owl-name='sale_char'>
{{--                        <chart-component--}}
{{--                            :table_id='"chart_financial_bar"'--}}
{{--                            :type='"bar"'--}}
{{--                            :title='"業績統計"'--}}
{{--                            :ajax_url='"/bonus/review/getAjaxData"'--}}
{{--                            :csrf='"{{csrf_token()}}"'--}}
{{--                            :labels='[]'--}}
{{--                            :chart_data='{{ json_encode([ ["data"=>0],["data"=>0],["data"=>0,"type"=> 'line'] ]) }}'--}}
{{--                        ></chart-component>--}}
                        <chart-component
                            :table_id='"chart_money_status"'
                            :type='"pie"'
                            :title='"金流狀態"'
                            :labels='["未收款","已收款"]'
                            :ajax_url='"/bonus/review/getAjaxData"'
                            :csrf='"{{csrf_token()}}"'
                            :chart_data='{{ json_encode([ ["data"=>[0,0,0,0]] ]) }}'
                        ></chart-component>
                    </div>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">業績列表</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"
                                data-ui-contorl='sale_list'><i
                                class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <div class="box-body">
                    <div class='owl-carousel owl-theme' data-owl-name='sale_list'>
                        <div class="col-xs-12">
                            <simple-data-table-componet
                                :table_id='"progress_list"'
                                :table_head='"成員績效表"'
                                :table_title='["月份","業務","團隊","毛利","達成率","%","獎金","英雄榜"]'
                                :row='[]'
                                :ex_buttons= '["excel"]'
                                :csrf='"{{csrf_token()}}"'
                                :columns='{!!json_encode($progressColumns)!!}'
                                :ajax_url='"/bonus/review/getAjaxData"'
                                :page_length='15'
                                :length_change='"hide"'
                            ></simple-data-table-componet>
                        </div>
                        <div class="col-xs-12">
                            <simple-data-table-componet
                                :table_id='"progress_list_total"'
                                :table_head='"成員總計績效表"'
                                :table_title='["業務","團隊","毛利","達成率","責任額"]'
                                :row='[]'
                                :ex_buttons= '["excel"]'
                                :csrf='"{{csrf_token()}}"'
                                :columns='{!!json_encode($progressTotalColumns)!!}'
                                :ajax_url='"/bonus/review/getAjaxData"'
                                :page_length='15'
                                :length_change='"hide"'
                            ></simple-data-table-componet>
                        </div>
                        <div class="col-xs-12">
                            <simple-data-table-componet
                                :table_id='"group_progress_list"'
                                :table_head='"團隊績效表"'
                                :table_title='["月份","團隊","毛利","達成率"]'
                                :row='[]'
                                :ex_buttons= '["excel"]'
                                :csrf='"{{csrf_token()}}"'
                                :columns='{!!json_encode($groupProgressColumns)!!}'
                                :ajax_url='"/bonus/review/getAjaxData"'
                                :page_length='15'
                                :length_change='"hide"'
                            ></simple-data-table-componet>
                        </div>
                        <div class="col-xs-12">
                            <simple-data-table-componet
                                :table_id='"group_progress_list_total"'
                                :table_head='"團隊總計績效表"'
                                :table_title='["團隊","毛利","達成率","責任額"]'
                                :row='[]'
                                :ex_buttons= '["excel"]'
                                :csrf='"{{csrf_token()}}"'
                                :columns='{!!json_encode($groupProgressTotalColumns)!!}'
                                :ajax_url='"/bonus/review/getAjaxData"'
                                :page_length='15'
                                :length_change='"hide"'
                            ></simple-data-table-componet>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">業績列表</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"
                                data-ui-contorl='detail_list'><i
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
                        :row='[]'
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
    <!-- /.row -->
@endsection

@section('script')
    <!-- page script -->
    <script type="text/javascript">

        $(document).ready(function () {
            /*紀錄 user 開關哪個box 存cookie 下次自動還原習用者習慣*/
            if (cookie.bonus_review === undefined) {
                cookie.bonus_review = {};
            }

            let boxToggleCookie = cookie.bonus_review;
            $('button[data-ui-contorl]').click(function () {
                let field = $(this).data('ui-contorl');
                eval(`boxToggleCookie.${field} = $(this).children('i').hasClass('fa-plus') ? 1 : 0;`);
                setCookie('ui-contorl', JSON.stringify(cookie));
            });

            if (boxToggleCookie !== undefined) {
                Object.keys(boxToggleCookie).forEach(key => {
                    if (boxToggleCookie[key] === 0) {
                        $('*[data-ui-contorl="' + key + '"]').parents('.box-header.with-border').parent().addClass('collapsed-box');
                        $('*[data-ui-contorl="' + key + '"]').children('i.fa').removeClass('fa-minus').addClass('fa-plus');
                    }
                });
            }


            var $owl = $('.owl-carousel').owlCarousel({
                loop: false,
                center: true,
                items: 1,
                margin: 10,
                autoHeight:true,
                responsive: {
                    800: {
                        items: 1
                    }
                }
            });

            async function sleep(ms = 0) {
                return new Promise(r => setTimeout(r, ms));
            }

            async function run() {
                await sleep(1500);
                $owl.trigger('refresh.owl.carousel');
            }

            run();
            /*cookie 紀錄使用者 習慣看哪個 owl item*/
            if (cookie.bonus_review_owl === undefined) {
                cookie.bonus_review_owl = {};
            }
            let owlShowItemCookie = cookie.bonus_review_owl;
            $('.owl-carousel').on('changed.owl.carousel', function (event) {
                eval(`owlShowItemCookie.${$(this).data('owlName')} = event.item.index;`);
                setCookie('ui-contorl', JSON.stringify(cookie));
            });
            if (owlShowItemCookie !== undefined) {
                Object.keys(owlShowItemCookie).forEach(key => {
                    if (owlShowItemCookie[key] !== 0) {
                        $('.owl-carousel[data-owl-name="' + key + '"]').data('owl.carousel').to(owlShowItemCookie[key]);
                    }
                });
            }

        });
    </script>
@endsection

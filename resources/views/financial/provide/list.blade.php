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
                <div class="small-box no-shadow col-md-4 col-sm-6 col-xs-12">
                    <div class="inner text-center">
                        <h3 id='total_money'>0</h3>
                        <input type='hidden' name='totalBonus' value='0'>
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
    {{--list --}}
    <div class='row'>
        <div class='col-md-12'>
            <provide-data-table-component :arg='{
            "csrf_token" : "{{csrf_token()}}",
            "paginate_count": {{$paginate->count()}},
            "paginate" : {
                "hasPages" : "{{$paginate->hasPages()}}",
                "onFirstPage" : "{{$paginate->onFirstPage()}}",
                "previousPageUrl" : "{{$paginate->previousPageUrl()}}",
                "currentPage" : "{{$paginate->currentPage()}}",
                "hasMorePages" : "{{$paginate->hasMorePages()}}",
                "nextPageUrl" : "{{$paginate->nextPageUrl()}}",
                "element" : {{ json_encode($paginateElement) }}
            },
            "sort" : "{{$sort}}",
            "sort_by" : "{{$sort_by}}",
            "show_item" : "{{$paginate->perPage()}}",
            "search_str" : "{{$search_str}}",
            "first_item_num" : {{$paginate->firstItem() ?? 0}},
            "last_item_num" : {{$paginate->lastItem() ?? 0 }},
            "total_item_num" : {{$paginate->total() ?? 0}},
            "row" : {{ json_encode($row)}},
            "users" : {{ json_encode(session('users'))}},
            "all_ids" : {!!  json_encode($allId)  !!},
            "select_ids" : {!! json_encode($selectIds) !!},
            "original_select_financial_id" : {!! json_encode($selectIds) !!},
            "total_alreday_select_money" : {{ $totalAlredaySelectMoney }}
                    }'></provide-data-table-component>
            {{--<div class='content table mdl-shadow--2dp mdl-data-table--selectable mdl-shadow--2dp col-md-12'>--}}
            {{--    --}}{{--				top element--}}
            {{--    <form action="#">--}}

            {{--        <div class="col-sm-6 p-20">--}}
            {{--            <div class="dataTables_length" id="example1_length">--}}
            {{--                <label>Show--}}
            {{--                    <select name="show_length"--}}
            {{--                            aria-controls="show_length"--}}
            {{--                            class="form-control input-sm">--}}
            {{--                        <option value="50" {{$paginate->count() == 50 ? 'selected' : ''}}>50</option>--}}
            {{--                        <option value="100" {{$paginate->count() == 100 ? 'selected' : ''}}>100</option>--}}
            {{--                        <option value="200" {{$paginate->count() == 200 ? 'selected' : ''}}>200</option>--}}
            {{--                    </select>--}}
            {{--                    entries</label>--}}
            {{--            </div>--}}
            {{--        </div>--}}
            {{--        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label pull-right">--}}

            {{--            <input class="mdl-textfield__input" type="text" id="search">--}}

            {{--            <label class="mdl-textfield__label" for="search">搜尋</label>--}}
            {{--        </div>--}}
            {{--    </form>--}}
            {{--    --}}{{-- level 1 head --}}
            {{--    <div class='table head'>--}}

            {{--        <div class='columm point col-md-1'>--}}
            {{--            <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="checkbox-all">--}}
            {{--                <input type="checkbox" id="checkbox-all" class="mdl-checkbox__input">--}}
            {{--            </label>--}}
            {{--        </div>--}}
            {{--        <div class='columm point col-md-2' data-sort='erp_user_id'>--}}
            {{--            業務--}}
            {{--            <div class='arrow pull-right {{$sort_by == 'erp_user_id' ? ($sort =='ASC' ? 'sorting_asc' : 'sorting_desc'):'sorting'}}'></div>--}}
            {{--        </div>--}}
            {{--        <div class='columm point col-md-5' data-sort='campaign_name'>--}}
            {{--            案件名稱--}}
            {{--            <div class='arrow pull-right {{$sort_by == 'campaign_name' ? ($sort =='ASC' ? 'sorting_asc' : 'sorting_desc'):'sorting'}}'></div>--}}
            {{--        </div>--}}
            {{--        <div class='columm point col-md-4'>--}}
            {{--            已選金額--}}
            {{--            --}}{{--<div class='arrow pull-right sorting'></div>--}}
            {{--        </div>--}}
            {{--        <div class='border-bottom'></div>--}}
            {{--    </div>--}}
            {{--    --}}{{-- level 1 body--}}
            {{--    <div class='body text-center' id='table_body'>--}}
            {{--        --}}{{--loading--}}
            {{--        <div class="mdl-spinner mdl-js-spinner loading is-active hidden" id='loading'></div>--}}
            {{--        --}}{{--message--}}
            {{--        <div class="loading hidden" id='nodata_message'>無資料</div>--}}
            {{--        @foreach($row as $erpUserId => $groupByUsers)--}}
            {{--            @foreach($groupByUsers as $campaignId => $groupByCampaigns)--}}
            {{--            <div class='item'>--}}
            {{--                <div class='columm col-md-1'>--}}
            {{--                    <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="checkbox-level2-{{$erpUserId}}-{{$campaignId}}">--}}
            {{--                        <input type="checkbox" id="checkbox-level2-{{$erpUserId}}-{{$campaignId}}" class="mdl-checkbox__input level2" data-id='1'>--}}
            {{--                    </label>--}}
            {{--                </div>--}}
            {{--                <div class='columm col-md-2 point'>--}}
            {{--                    {{session('users')[$erpUserId]['name']}}--}}
            {{--                </div>--}}
            {{--                <div class='columm col-md-5 point'>--}}
            {{--                    {{$groupByCampaigns[0]['campaign_name']}}--}}
            {{--                </div>--}}
            {{--                <div class='columm col-md-4 point'>--}}
            {{--                    0--}}
            {{--                </div>--}}
            {{--                <input type='hidden' name='campaignBonus' value='0'>--}}
            {{--                <div class='border-bottom'></div>--}}
            {{--                --}}{{--level2--}}
            {{--                <div class='treeview-items'>--}}
            {{--                    --}}{{--level2 head--}}
            {{--                    <div class='head'>--}}
            {{--                        <div class='columm col-md-1'>--}}
            {{--                            --}}{{--預留--}}
            {{--                        </div>--}}
            {{--                        <div class='columm col-md-2'>--}}
            {{--                            月份--}}
            {{--                        </div>--}}
            {{--                        <div class='columm col-md-2'>--}}
            {{--                            媒體--}}
            {{--                        </div>--}}
            {{--                        <div class='columm col-md-2'>--}}
            {{--                            賣法--}}
            {{--                        </div>--}}
            {{--                        <div class='columm col-md-1'>--}}
            {{--                            %--}}
            {{--                        </div>--}}
            {{--                        <div class='columm col-md-2'>--}}
            {{--                            毛利--}}
            {{--                        </div>--}}
            {{--                        <div class='columm col-md-2'>--}}
            {{--                            獎金--}}
            {{--                        </div>--}}
            {{--                        <div class='border-bottom'></div>--}}
            {{--                    </div>--}}
            {{--                    							--}}{{--level2 body--}}
            {{--                    <div class='body'>--}}
            {{--                        @foreach($groupByCampaigns as $index => $cue)--}}
            {{--                        <div class='item'>--}}
            {{--                            <div class='columm col-md-1'>--}}
            {{--                                <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect children_select"--}}
            {{--                                       for="checkbox-{{$cue['id']}}">--}}
            {{--                                    <input type="checkbox" id="checkbox-{{$cue['id']}}" class="mdl-checkbox__input" value={{$cue['id']}}>--}}
            {{--                                </label>--}}
            {{--                            </div>--}}
            {{--                            <div class='columm col-md-2'>--}}
            {{--                                {{$cue['set_date']}}--}}
            {{--                            </div>--}}
            {{--                            <div class='columm col-md-2'>--}}
            {{--                                {{$cue['media_channel_name']}}--}}
            {{--                            </div>--}}
            {{--                            <div class='columm col-md-2'>--}}
            {{--                                {{$cue['sell_type_name']}}--}}
            {{--                            </div>--}}
            {{--                            <div class='columm col-md-1'>--}}
            {{--                                5%--}}
            {{--                            </div>--}}
            {{--                            <div class='columm col-md-2'>--}}
            {{--                                {{$cue['profit']}}--}}
            {{--                            </div>--}}
            {{--                            <div class='columm col-md-2'>--}}
            {{--                                $50--}}
            {{--                            </div>--}}
            {{--                            <input type='hidden' name='bonus' value=50>--}}
            {{--                            <div class='border-bottom'></div>--}}
            {{--                        </div>--}}
            {{--                        @endforeach--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--            </div>--}}
            {{--            @endforeach--}}
            {{--        @endforeach--}}
            {{--    </div>--}}
            {{--    <div class='footer'>--}}
            {{--        <div class="col-sm-4"><div class="dataTables_info" id="example1_info" role="status" aria-live="polite">Showing {{$paginate->firstItem()}} to {{$paginate->lastItem()}} of {{$paginate->total()}} entries</div></div>--}}
            {{--        <div class="col-sm-12 text-center">--}}
            {{--            {{ $paginate->onEachSide(1)->links() }}--}}
            {{--        </div>--}}
            {{--        <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored pull-right">--}}
            {{--            送出--}}
            {{--        </button>--}}
            {{--    </div>--}}
            {{--</div>--}}

        </div>
        <!-- Colored raised button -->

    </div>

@endsection

@section('script')

    <!-- page script -->
    <script>

        // function next(){
        //     axios.post('/financial/provide/list?page=3',{
        //     }).then(
        //         response => {
        //             selectFinancialId = response.data;
        //             console.log(selectFinancialId);
        //         },
        //         err => {
        //             reject(err);
        //         }
        //     );
        // }



        // function changeUrlParmas(data) {
        //     window.history.pushState('', '', '?' + $.param(data));
        // }

        // function getAjaxData(urlParmas) {
        //
        //     $('#loading').removeClass('hidden');
        //     $('#table_body').children('.item').remove();
        //
        //     axios.post('/financial/provide/ajaxData',
        //         urlParmas,
        //     ).then(
        //         response => {
        //             rowData = response.data;
        //             if( typeof rowData.date === undefined){
        //                 $('#nodata_message').removeClass('hidden');
        //             }else{
        //                 $('#nodata_message').addClass('hidden');
        //             }
        //
        //             $('#loading').addClass('hidden');
        //             // console.log(rowData);
        //         },
        //         err => {
        //             reject(err);
        //         }
        //     );
        // }

        $(document).ready(function () {
            {{--var totalBonus = 0;--}}
            {{--var selectFinancialId = [];--}}
            {{--var allId = {!!  json_encode($allId)  !!};--}}

            // $('.row').on('change','select[name="show_length"]',function(index){
            //     let urlParmas = getUrlParmas();
            //     urlParmas.showItem = $(this).val();
            //
            //     getAjaxData(urlParmas);
            //
            //     changeUrlParmas(urlParmas);
            //
            // });
            // all select
            // $('.row').on('click', '#checkbox-all', function (index) {
            //     let checkStatus = $(this).prop("checked");
            //     toggleCheck(checkStatus, $('input:checkbox'));
            //
            //     if (checkStatus) {
            //         selectFinancialId = allId;
            //         console.log(selectFinancialId);
            //     } else {
            //         selectFinancialId = [];
            //         console.log(selectFinancialId);
            //     }
            // });

            //level2 select
            // $('.row').on('click', '.mdl-checkbox__input.level2', function (index) {
            //
            //     let checkStatus = $(this).prop("checked");
            //     let targetDom = $(this).parents('.item').find('.treeview-items').find('input:checkbox');
            //
            //     toggleCheck(checkStatus, targetDom);
            //
            //     selectIdValue(checkStatus,targetDom);
            //
            // });

            //level2 children select self
            // $('.row').on('click', '.treeview-items>.body>.item', function (index) {
            //
            //     let targetDom = $(this).find('input:checkbox');
            //     let checkStatus = !targetDom.prop("checked");
            //
            //     toggleCheck(checkStatus, targetDom);
            //     selectIdValue(checkStatus,targetDom);
            //
            //     let campaignBonus = $(this).parents('.item').find('input[name="campaignBonus"]');
            //     let checkBoxDom = $(this).parent().find('input:checkbox');
            //
            //     calculatBonus(checkBoxDom,campaignBonus);
            //
            // });

            // function calculatBonus(checkBoxDom,tagretDom){
            //     tagretDom.val(0);
            //     checkBoxDom.map((index,val)=>{
            //
            //         if($(val).prop("checked")){
            //             let nowBonus = parseInt(tagretDom.val());
            //             nowBonus += parseInt(checkBoxDom.find('input[name="bonus"]').val());
            //             tagretDom.val(nowBonus);
            //         }
            //     });
            // }
            // function toggleCheck(checkStatus, targetDom) {
            //     if (checkStatus) {
            //         targetDom.prop("checked", true);
            //         targetDom.parents('label').addClass('is-checked');
            //         console.log('select1');
            //     } else {
            //         targetDom.prop("checked", false);
            //         targetDom.parents('label').removeClass('is-checked');
            //         console.log('not select1');
            //     }
            // }
            // function selectIdValue(checkStatus,targetDom){
            //
            //     if(checkStatus){
            //         targetDom.map(function(index){
            //             index = selectFinancialId.indexOf(parseInt($(this).val()));
            //             if (index == -1) {
            //                 selectFinancialId.push(parseInt($(this).val()));
            //             }
            //         });
            //         console.log(selectFinancialId);
            //     }else{
            //         targetDom.map(function(index){
            //             index = selectFinancialId.indexOf(parseInt($(this).val()));
            //
            //             if (index > -1) {
            //                 selectFinancialId.splice(index, 1);
            //             }
            //         });
            //         console.log(selectFinancialId);
            //     }
            // }
            //treeview-items slide
            // $('.row').on('click', '.item>.point', function (index) {
            //     let tragetDom = $(this).parent().children('.treeview-items');
            //     if (tragetDom.hasClass('is-show')) {
            //         tragetDom.slideUp();
            //         tragetDom.removeClass('is-show');
            //     } else {
            //         tragetDom.slideDown();
            //         tragetDom.addClass('is-show');
            //     }
            // });

            //sorting
            // $('.row').on('click', '.table.head>.columm', function (index) {
            //     let target = $(this).children('.arrow');
            //     let sorting = target.hasClass('sorting');
            //     let sorting_asc = target.hasClass('sorting_asc');
            //     let sorting_desc = target.hasClass('sorting_desc');
            //     sortingReset(target);
            //
            //     //get url Parmas
            //     let urlParmas = getUrlParmas();
            //
            //     if(sorting){
            //         target.removeClass('sorting');
            //         target.addClass('sorting_asc');
            //         //change parmas data
            //         urlParmas['sort_by'] = $(this).data('sort');
            //         urlParmas['sort'] = 'ASC';
            //     }
            //     if(sorting_asc){
            //         target.removeClass('sorting_asc');
            //         target.addClass('sorting_desc');
            //         //change parmas data
            //         urlParmas['sort_by'] = $(this).data('sort');
            //         urlParmas['sort'] = 'DESC';
            //     }
            //     if(sorting_desc){
            //         target.removeClass('sorting_desc');
            //         target.addClass('sorting');
            //         //empty parmas data
            //         delete urlParmas['sort_by'];
            //         delete urlParmas['sort'];
            //     };
            //     //get data
            //     getAjaxData(urlParmas);
            //     //change Url parmas
            //     changeUrlParmas(urlParmas);
            // });
            // function sortingReset(target){
            //     $('.arrow').addClass('sorting');
            //     $('.arrow').removeClass('sorting_asc');
            //     $('.arrow').removeClass('sorting_desc');
            //     target.removeClass('sorting');
            // };
            // function sleep (time) {
            //     return new Promise((resolve) => setTimeout(resolve, time));
            // }
            //search
            // var evTimeStamp = 0;

            // $('.row').on('keyup','#search',function(){
            //     setTimeout(()=>{
            //         var now = +new Date();
            //         if (now - evTimeStamp < 1200) {
            //             return;
            //         }
            //         evTimeStamp = now;
            //         //get url Parmas
            //         let urlParmas = getUrlParmas();
            //         delete urlParmas['searchStr'];
            //         delete urlParmas['page'];
            //         urlParmas['searchStr'] = $(this).val();
            //
            //         getAjaxData(urlParmas);
            //         changeUrlParmas(urlParmas);
            //     },1000)
            // });
        });
    </script>
@endsection

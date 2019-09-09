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
    <div class='row'>
        <div class="box box-widget widget-user">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-aqua">
                <!-- small box -->
                <div class="small-box no-shadow col-md-6 col-sm-6 col-xs-12">
                    <div class="inner text-center">
                        <h3 id='total_money'>4000</h3>
                        <input type='hidden' name='totalBonus' value='0'>
                        <p>總計發放金額</p>
                    </div>
                </div>
                <div class="form-group col-xs-8 col-md-6 pull-left">
                    <date-range :start_date='"2019-08"' :end_date='"2019-09"'></date-range>
                </div>
                {{--<div class="form-group col-xs-8 col-md-6 pull-left">--}}
                {{--    <label>月份</label>--}}
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
            <div class="box-footer">
                <div class="row">
                    <div class="col-lg-6">
                        <label>選擇成員</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name='selectType' data-type='select_user'>
                            </span>
                            <select class="form-control select2" id='select_user' multiple="multiple" data-placeholder="選擇成員" style="width: 100%;">
                                @foreach( $userList as $item)
                                    <option value='{{$item['id']}}'> {{$item['name']}} </option>
                                @endforeach
                            </select>
                            <!-- /.user-select-block -->
                        </div>
                        <!-- /input-group -->
                    </div>
                    <!-- /.col-lg-6 -->
                    @if($saleGroups)
                    <div class="col-lg-6">
                        <label>選擇團隊</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name='selectType' data-type='select_groups'>
                            </span>
                            <select class="form-control select2" id='select_groups' multiple="multiple" data-placeholder="選擇團隊" style="width: 100%;">
                                @foreach( $saleGroups as $item)
                                    <option value='{{$item['id']}}'> {{$item['name']}} </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- /input-group -->
                    </div>
                    <!-- /.col-lg-6 -->
                        @endif
                </div>
                {{--<div class="row">--}}
                {{--    <div class="col-sm-12 border-right">--}}
                {{--        <div class="form-group col-xs-12">--}}
                {{--            <label>檢視人員</label>--}}
                {{--            <select class="form-control select2" multiple="multiple" data-placeholder="Select a State" style="width: 100%;">--}}
                {{--                @foreach( session('users') as $item)--}}
                {{--                    <option value='{{$item['id']}}'> {{$item['name']}} </option>--}}
                {{--                @endforeach--}}
                {{--            </select>--}}
                {{--        </div>--}}
                {{--        <!-- /.user-select-block -->--}}
                {{--    </div>--}}
                {{--    <!-- /.col -->--}}
                {{--</div>--}}
                <!-- /.row -->
            </div>
        </div>
        <simple-data-table-componet
                :table_id='"provide_bonus_table"'
                :table_head='"獎金清單"'
                :table_title='["發放月份","月份","案件名稱","業務","部門","媒體","類型","獎金"]'
                :row = '{{ json_encode($row) }}'
                :ex_buttons= '["excel"]'
                :columns = {!!json_encode($columns)!!}
        ></simple-data-table-componet>
    </div>
    <!-- /.row -->
@endsection

@section('script')

    <!-- page script -->
    <script>
        $(function () {
            $('.select2').select2();
            var evTimeStamp = 0;
            $('.row').on('click','input[name="selectType"]',function(v,k){
                let now = +new Date();
                if (now - evTimeStamp < 100) {
                    return;
                }
                evTimeStamp = now;
                let select_user = $('#select_user');
                let select_groups = $('#select_groups');
                let type = $(this).data('type');
                switch (type){
                    case 'select_user':
                        select_user.attr('disabled',false);
                        select_groups.attr('disabled',true).val(null).trigger("change");
                        break;
                    case 'select_groups':
                        select_groups.attr('disabled',false);
                        select_user.attr('disabled',true).val(null).trigger("change");
                        break;
                }
            });
        });
    </script>
@endsection

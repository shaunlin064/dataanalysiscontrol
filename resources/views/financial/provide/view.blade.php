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
                    <date-range :start_date='"{{date('Y-m')}}"' :end_date='"{{date('Y-m')}}"'></date-range>
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
                    @if($saleGroups)
                    <div class="col-lg-6">
                        <label>選擇團隊</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="radio" name='selectType' data-type='select_groups' @if($saleGroups){{'checked'}}@endif >
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
                        @endif
                </div>
            </div>
        </div>

        <simple-data-table-componet
                :table_id='"provide_sale_groups_bonus_table"'
                :table_head='"招集人獎金"'
                :table_title='["發放月份","月份","業務","團隊名稱","類型","團隊毛利","獎金比例","獎金"]'
                :row = '{{ json_encode($saleGroupsReach) }}'
                :ex_buttons= '["excel"]'
                :columns = '{!!json_encode($saleGroupsReachColumns)!!}'
        ></simple-data-table-componet>

        <simple-data-table-componet
                :table_id='"provide_bonus_table"'
                :table_head='"獎金清單"'
                :table_title='["發放月份","月份","案件名稱","業務","部門","媒體","類型","獎金"]'
                :row = '{{ json_encode($provideBonus) }}'
                :ex_buttons= '["excel"]'
                :columns = {!!json_encode($provideBonusColumns)!!}
        ></simple-data-table-componet>
    </div>
    <!-- /.row -->
@endsection

@section('script')

    <!-- page script -->
    <script>
        // $(function () {
        //     var evTimeStamp = 0;
        //     $('.row').on('click','input[name="selectType"]',function(v,k){
        //         let now = +new Date();
        //         if (now - evTimeStamp < 100) {
        //             return;
        //         }
        //         evTimeStamp = now;
        //         let select_user = $('#select_user');
        //         let select_groups = $('#select_groups');
        //         let type = $(this).data('type');
        //         switch (type){
        //             case 'select_user':
        //                 select_user.attr('disabled',false);
        //                 select_groups.attr('disabled',true).val(null).trigger("change");
        //                 select_groups.next().find('li.select2-selection__choice').remove();
        //                 select_groups.next().find('.select2-search__field').attr('placeholder','選擇團隊').css('width','415px');
        //                 break;
        //             case 'select_groups':
        //                 select_groups.attr('disabled',false);
        //                 select_user.attr('disabled',true).val(null).trigger("change");
        //                 select_user.next().find('li.select2-selection__choice').remove();
        //                 select_user.next().find('.select2-search__field').attr('placeholder','選擇成員').css('width','415px');
        //                 break;
        //         }
        //     });
        // });
    </script>
@endsection

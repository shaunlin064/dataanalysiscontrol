<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-07-29
	 * Time: 10:44
	 */
?>

@extends('layout')

@section('title','DAC | 匯率設定')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        匯率設定
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">匯率設定</li>
    </ol>
@endsection

@extends('headerbar')


<!-- Content Wrapper. Contains page content -->
@section('content')
    <div class="row" id='bonusSetting'>
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title pull-left">設定清單</h3>

                </div>

                <!-- /.box-header -->
                <div class="box-body">
                    <div class="col-md-12">
                        <!-- Horizontal Form -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title"></h3>
                                @if($errors->any())
                                    @foreach ($errors->all() as $error)
                                        <div class='alert-danger text-center'>{{ $error }}</div>
                                    @endforeach
                                @endif
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form class="form-horizontal" action='add' method='post' id='exchangeRateForm'>
                                <div class="box-body">
                                    @csrf
                                    <div class="form-group">

                                        <label for='set_date-datepicker' class="col-sm-1 control-label">月份</label>
                                        <div class="col-sm-3">
                                            <date-picker-component :dom_id='"set_date"'></date-picker-component>
                                        </div>

                                        <label for='selectCurrency' class="col-sm-1 control-label">幣別</label>
                                        <div class="col-sm-3">
                                            <select class="form-control" id='currency' name='currency'>
                                                @foreach($currencys as $currency)
                                                <option value={{$currency}}>{{$currency}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label for="inputRate" class="col-sm-1 control-label">匯率</label>
                                        <div class="col-sm-3">
                                            <input type="number" step='0.00000001' class="form-control" id="rate" name='rate' placeholder="匯率" required>
                                        </div>
                                    </div>
                                    <div class='form-group'>

                                    </div>
                                </div>
{{--                                <span class="center-block text-center">當前設定月份為 {{date('Y-m',strtotime("-1 month"))}}</span>--}}
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-info pull-right">送出</button>
                                </div>
                                <!-- /.box-footer -->
                            </form>
                        </div>
                        <!-- /.box -->
                    </div>
                    <table id="exchangeTable" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>月份</th>
                            <th>幣別</th>
                            <th>匯率</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>月份</th>
                            <th>幣別</th>
                            <th>匯率</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
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
            {{--var users = {!! json_encode(session('users')) !!};--}}
                    {{--var departments = {!! json_encode(session('department')) !!};--}}

            var dataTable = $('#exchangeTable').DataTable({
                    'paging'      : true,
                    'ordering'    : true,
                    'info'        : true,
                    'autoWidth'   : false,
                    "aLengthMenu" : [25, 50, 100], //更改顯示記錄數選項
                    "oLanguage": {
                        "emptyTable"    : "目前沒有任何（匹配的）資料。",
                        "sProcessing":   "處理中...",
                        "sLengthMenu":   "顯示 _MENU_ 項結果",
                        "sZeroRecords":  "沒有資料",
                        "sInfo":         "顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項",
                        "sInfoEmpty":    "顯示第 0 至 0 項結果，共 0 項",
                        "sInfoFiltered": "(從 _MAX_ 項結果過濾)",
                        "sInfoPostFix":  "",
                        "sSearch":       "搜索:",
                        "sUrl":          "",
                        "oPaginate": {
                            "sFirst":    "首頁",
                            "sPrevious": "上頁",
                            "sNext":     "下頁",
                            "sLast":     "尾頁"
                        }
                    },
                    "order": [[ 0, 'desc' ]],
                    columns: [
                        {
                            data: "set_date"
                        },
                        { data: 'currency',},
                        { data: 'rate'},
                    ],
                });
            let listdata = {!! json_encode($row)  !!};

            dataTable.clear();
            dataTable.rows.add( listdata );
            dataTable.draw();

            $('#bonusSetting').on('click','#addNewUser',function(){
                window.open('/bonus/setting/add');
            });

        })
    </script>
@endsection


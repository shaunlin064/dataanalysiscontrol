<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-07-29
	 * Time: 10:44
	 */

	$dateLastMonth = new DateTime();
	$dateLastMonth = $dateLastMonth->modify('-1Month')->format('Y/m');

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
    <div class="row" id='exchangeRateSetting'>
        <div class="col-xs-12">
            <div class="box">
                @can('financial.exchangeRate.setting')
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
                            <form class="form-horizontal" action='add' method='post' id='exchangeRateForm' data-step="1" data-intro="幣別月份設定不能重複,須注意在時間內輸入匯率..<a href='/info/scheduleList'>可參考系統排程表</a>">
                                <div class="box-body">
                                    @csrf
                                    <div class="form-group">

                                        <label for='set_date-datepicker' class="col-sm-1 control-label">月份</label>
                                        <div class="col-sm-3">
                                            <date-picker-component
                                                    :dom_id='"set_date"'
                                                    :date='"{{$dateLastMonth}}"'
                                            ></date-picker-component>
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
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-info pull-right">送出</button>
                                </div>
                                <!-- /.box-footer -->
                            </form>
                        </div>
                        <!-- /.box -->
                    </div>
                </div>
                @endcan
                <simple-data-table-componet
                        :table_id='"exchangeTable"'
                        :table_head='"財報清單"'
                        :table_title='["月份","幣別","匯率"]'
                        :row = '{!! json_encode($row) !!}'
                        :columns = '{!!  json_encode(([['data'=>"set_date"],['data'=>"currency"],['data'=>"rate"]]))!!}'
                ></simple-data-table-componet>
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
        })
    </script>
@endsection


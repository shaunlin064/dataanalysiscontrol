<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-08-27
	 * Time: 16:37
	 */

?>

@extends('layout')

@section('title','DAC | 編輯團隊設定')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        編輯團隊設定
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">編輯團隊設定</li>
    </ol>
@endsection

@extends('headerbar')


<!-- Content Wrapper. Contains page content -->
@section('content')
    <div class="row">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active" ><a href="#settings" data-toggle="tab" data-step="1" data-intro="設定召集人團隊">設定</a></li>
                <li class="" ><a href="#hirstory" data-toggle="tab" data-step="10" data-intro="歷史資料">歷史資料</a></li>
                {{--<li :class="{'active':type == 'view'}" v-if="type == 'edit' || type == 'view'" ><a href="#hirstory" data-toggle="tab">歷史資料</a></li>--}}
            </ul>
            <div class="tab-content">
                <sale-group-form-component
                        :arg="{
                            csrf_token:'{{ csrf_token()}}',
                            date : '{{ date('Y-m') }}',
                            user_now_select : '{{json_encode($userNowSelect)}}',
                            user_now_select_is_convener : '{{json_encode($userNowIsConvener)}}',
                            userdata : '{{ json_encode($user) }}',
                            form_action:'{{Route('saleGroup.setting.post')}}',
                            row: '{{ json_encode($row) }}',
                            sale_group_id: '{{$row['id']}}',
                            name: '{{$row['name']}}',
                            bonus : '{{ json_encode($row['groups_bonus']) }}',
                            groups_users: '{{json_encode($row['groups_users'])}}',
                            total_boundary : '{{$totalBoundary}}'
                        }"
                ></sale-group-form-component>
                <div class="tab-pane" id="hirstory">
                    <!-- /.tab-pane -->
                        <!-- Date range -->
                        {{--<div class="form-group">--}}
                        {{--    <label>Date range:</label>--}}

                        {{--    <div class="input-group">--}}
                        {{--        <div class="input-group-addon">--}}
                        {{--            <i class="fa fa-calendar"></i>--}}
                        {{--        </div>--}}
                        {{--        <input type="text" class="form-control pull-right" id="reservation">--}}
                        {{--    </div>--}}
                        {{--    <!-- /.input group -->--}}
                        {{--</div>--}}
                        <!-- /.form group -->
                    {{--History Start--}}
                        @foreach( $groupsBonusHistory as $key => $items)
                        <div class="box box-warning collapsed-box">
                            <div class="box-header with-border">
                                <h3 class="box-title">{{$key}}</h3>

                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                    </button>
                                </div>
                                <!-- /.box-tools -->
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="box box-widget widget-user">
                                    <!-- Add the bg color to the header using any of the bg-* classes -->
                                    <div class="widget-user-header bg-aqua-active">
                                        <div class="col-sm-6 border-right">
                                            <div class="description-block">
                                                <h5 class="">獎金比例</h5>
                                                <h3 class="description-text">
                                                    {{$items['rate'].'%' }}
                                                </h3>
                                            </div>
                                            <!-- /.description-block -->
                                        </div>
                                        <!-- /.col -->
                                        {{--<div class="col-sm-4 border-right">--}}
                                        {{--    <div class="description-block">--}}
                                        {{--        <h5 class="">團隊名稱</h5>--}}
                                        {{--        <h3>{{$row['name']}}</h3>--}}
                                        {{--    </div>--}}
                                        {{--    <!-- /.description-block -->--}}
                                        {{--</div>--}}
                                        <!-- /.col -->
                                        <div class="col-sm-6">
                                            <div class="description-block">
                                                <h5 class="">責任額總計</h5>
                                                <h3 class="description-text" id='total_boundary'>
                                                    {{$items['totalBoundary']
                                                     }}
                                                </h3>
                                            </div>
                                            <!-- /.description-block -->
                                        </div>
                                        <!-- /.col -->
                                    </div>
                                </div>
                                <h4>團為績效位階
                                    <span class="pull-right"></span>
                                </h4>
                                <div class="box">
                                    <div class="box-header">
                                        <h3 class="box-title">獎金級距</h3>
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body no-padding">
                                        <table class="table table-condensed">
                                            <tbody>
                                                <tr>
                                                    <th style="width: 10px">#</th>
                                                    <th>達成比例</th>
                                                    <th>額外獎金</th>
                                                </tr>
                                                @if(isset($items['bonuslevel']))
                                                @foreach($items['bonuslevel'] as $item)
                                                <tr>
                                                    <td></td>
                                                    <td><span class='badge bg-light-blue'>{{$item['achieving_rate']}}%</span></td>
                                                    <td><span class="badge bg-red">${{$item['bonus_direct']}}</span></td>
                                                </tr>
                                                @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /.box -->
                                @foreach($items['user'] as $users)
                                    @if($users->is_convener == 0)
                                        <span class='badge bg-light-blue'>{{$users->name}}</span>
                                    @else
                                        <span class='badge bg-red'>{{$users->name}}</span>
                                    @endif
                                @endforeach
                            </div>
                            <!-- /.box-body -->

                        </div>
                        @endforeach
                        <!-- /.box -->
                    {{--History End--}}
                </div>
            </div>
            <!-- /.tab-content -->
        </div>
        <!-- /.nav-tabs-custom -->
    </div>
    <!-- /.row -->
@endsection

@section('script')

    <!-- page script -->
    <script>
        $(function () {

        });
    </script>
@endsection

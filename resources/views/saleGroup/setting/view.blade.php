<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-08-27
	 * Time: 16:37
	 */

?>

@extends('layout')

@section('title','DAC | 招集人設定檢視')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        招集人設定檢視
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">招集人設定檢視</li>
    </ol>
@endsection

@extends('headerbar')


<!-- Content Wrapper. Contains page content -->
@section('content')
    <div class="row">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active" ><a href="#hirstory" data-toggle="tab">歷史資料</a></li>
                {{--<li :class="{'active':type == 'view'}" v-if="type == 'edit' || type == 'view'" ><a href="#hirstory" data-toggle="tab">歷史資料</a></li>--}}
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="hirstory">
                    @foreach($row as $saleGroups)
                    <div class='box box-info'>
                        <div class='box-header with-border'>
                            <h3 class='box-title'>{{$saleGroups['name']}}</h3>
                        </div>

                        @foreach( $saleGroups['groupsUsersHistory'] as $key => $groupsUsersHistory)
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
                                                        {{5.5 - ($groupsUsersHistory->count() * 0.25).'%' }}
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
                                                        {{$groupsUsersHistory->map(function($v){
                                                                return $v->getUserBonusBoundary->boundary  ?? 0;
                                                            })->sum()
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

                                                @if(isset($saleGroups['groupsBonusHistory'][$key]))
                                                    @foreach($saleGroups['groupsBonusHistory'][$key] as $item)
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
                                    @foreach($groupsUsersHistory as $users)
                                        @if($users->is_convener == 0)
                                            <span class='badge bg-light-blue'>{{$users->user['name']}}</span>
                                        @else
                                            <span class='badge bg-red'>{{$users->user['name']}}</span>
                                        @endif
                                    @endforeach
                                </div>
                                <!-- /.box-body -->

                            </div>
                        @endforeach
                    <!-- /.box -->
                    </div>
                    @endforeach
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
            $('.box').on('click','.btn.btn-box-tool',function (v,k) {
               console.log($(this));
            });
        });
    </script>
@endsection

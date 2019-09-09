<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-08-26
	 * Time: 16:09
	 */
?>
@extends('layout')

@section('title','DAC | 新增團隊設定')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        新增團隊設定
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">新增團隊設定</li>
    </ol>
@endsection

@extends('headerbar')

<!-- Content Wrapper. Contains page content -->
@section('content')
    <div class="row">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active" ><a href="#settings" data-toggle="tab">設定</a></li>
                {{--<li :class="{'active':type == 'view'}" v-if="type == 'edit' || type == 'view'" ><a href="#hirstory" data-toggle="tab">歷史資料</a></li>--}}
            </ul>
            <div class="tab-content">
                <sale-group-form-component
                        :arg="{
                        csrf_token:'{{ csrf_token()}}',
                        form_action:'{{Route('saleGroup.setting.post')}}',
                        userdata : '{{ json_encode($user) }}'
                        }"
                        ></sale-group-form-component>
                {{--<bonus-history-component v-if="type == 'edit' || type == 'view'" :items='history' :active="type != 'edit'"></bonus-history-component>--}}
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

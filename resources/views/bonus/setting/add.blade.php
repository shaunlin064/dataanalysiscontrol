<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-25
	 * Time: 17:42
	 */
?>
<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-18
	 * Time: 15:33
	 */

?>
@extends('layout')

@section('title','DAC | 責任額設定')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        責任額新增
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">責任額設定新增頁面</li>
    </ol>
@endsection

@extends('headerbar')

@extends('sidebar')

<!-- Content Wrapper. Contains page content -->
@section('content')
    <div class="row">
        <!-- /.col -->
        <div class="col-md-12">
            <bonus-group-component :arg='{type:"add",add_user_lists:{{ json_encode($addUserLists) }},csrf_token:"{{ csrf_token()}}"}'></bonus-group-component>
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






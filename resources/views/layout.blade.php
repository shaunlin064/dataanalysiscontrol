<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019/06/14
	 * Time: 4:08 PM
	 */
	$css = $data['cssPath'];
	$js = $data['jsPath'];

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset='utf-8'>
	<meta http-equiv='X-UA-Compatible' content='IE=edge'>
	<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
	<META HTTP-EQUIV="EXPIRES" CONTENT="0">
	<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('title')</title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	@foreach($css as $path)
		<link rel='stylesheet' href='{{$path}}'>
	@endforeach
</head>
@include('loading')
<body class='hold-transition skin-blue sidebar-collapse sidebar-mini'>

<div id="app" class='app_hide'>

<div class='wrapper'>

	@yield('headerbar')
	<!-- Left side column. contains the logo and sidebar -->
	@include('sidebar')
	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->

			<section class="content-header">
				@yield('content-header')
			</section>

			<!-- Main content -->
		<section class="content">
			@yield('content')
		</section>
			@yield('invoice')

		<!-- /.content -->
	</div>
	<!-- /.content-wrapper -->

	{{--<footer class="main-footer">--}}
	{{--	<div class="pull-right hidden-xs">--}}
	{{--	</div>--}}
	{{--</footer>--}}



</div>
</div>

@foreach($js as $path)
	<script src='{{ $path }}'></script>
@endforeach

@yield('script')
</body>
</html>

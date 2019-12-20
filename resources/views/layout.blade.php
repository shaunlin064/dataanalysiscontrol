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
<body class='hold-transition skin-blue sidebar-mini'>

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

<script type="text/javascript">

    var cookie = JSON.parse(getCookie('ui-contorl') ? getCookie('ui-contorl') : '{}');

    $(document).on('click','.sidebar-toggle',function(){
        cookie.sidebar = $('body').hasClass('sidebar-collapse') ? 0 : 1;
        setCookie('ui-contorl',JSON.stringify(cookie));
    });

    if(cookie.sidebar == 0){
        $('body').addClass('sidebar-collapse');
    }

    function setCookie(name,value)//两个参数，一个是cookie的名子，一个是值
    {
        var Days = 30; //此 cookie 将被保存 30 天
        var exp  = new Date();    //new Date("December 31, 9998");
        exp.setTime(exp.getTime() + Days*24*60*60*1000);
        document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
    }
    function getCookie(name)//取cookies函数
    {
        var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
        if(arr != null) return unescape(arr[2]); return null;

    }
    function delCookie(name)//删除cookie
    {
        var exp = new Date();
        exp.setTime(exp.getTime() - 1);
        var cval=getCookie(name);
        if(cval!=null) document.cookie= name + "="+cval+";expires="+exp.toGMTString();
    }

</script>

</body>
</html>

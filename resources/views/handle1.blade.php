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
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    @foreach($css as $path)
        <link rel='stylesheet' href='{{$path}}'>
    @endforeach
    <style>
        .content-wrapper{
            min-height: 758px;
            margin: 0px;
        }
    </style>
</head>
<body class='hold-transition'>
<div id="app">
        <div class="content-wrapper ">

            <!-- Main content -->
            <section class="content">
                <div class="login-box">
                    <div class="login-logo">
                        <a href="#"><b>系統</b></a>
                    </div>
                    <!-- /.login-logo -->

                    <div class="login-box-body">
                        <p class="login-box-msg message_status">{{$message['status_string']}}</p>
                        <p class="login-box-msg">{{$message['message']}}</p>

                        <div class="social-auth-links text-center">
                            @if($message['status_string'] == "錯誤")
                                <button class="btn-default" id="close_error">關閉</button>
                            @endif
                        </div>
                        <!-- /.social-auth-links -->

                    </div>

                    <!-- /.login-box-body -->
                </div>
            </section>

        <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
</div>

@foreach($js as $path)
    <script src='{{ $path }}'></script>
@endforeach

<script>
    $(document).ready(function () {
        var return_url = "{{ $returnUrl }}";
        if(return_url == ''){
            return_url = "{{ route('index') }}";
        }

        if($(".message_status").html() != "錯誤") {
            setTimeout(function () {
                location.href = return_url;
            }, 2000);
        }

        $("#close_error").click(function(){
            location.href = return_url;
        });
    });

</script>
</body>
</html>

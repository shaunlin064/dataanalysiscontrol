<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-09-25
	 * Time: 09:35
	 */
?>
<style>
    #loading {position: absolute;
        width: 100%;
        height: 100%;
        background: rgb(255,137,148);
        z-index: 9999;
        color: #3c8dbc;
    }
    .load_content{
        margin: 10% 15%;
        height: 55%;
        display: grid;
    }
    #load_title{

    }
    #load_body{
        margin-top: 40%;
    }
    #loader-1 {
        font-size: 30px;
        line-height: 32px;
        font-weight: 500;
    }
    #nav-logo-w{
        display: grid;

    }
    /*@import url(https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900);*/

    html,body {
        margin: 0;
        padding: 0;
        font-family:'Lato', sans-serif;
    }
    .loader {
        width: 100px;
        height: 80px;
        position: absolute;
        top: 0; right: 0; left: 0; bottom: 0;
        margin: auto;
    }
    .loader .image {
        width: 100px;
        height: 160px;
        font-size: 40px;
        text-align: center;
        transform-origin: bottom center;
        transform: rotate(90deg);
        opacity: 0;
    }
    .rotate_animation{
        animation: 1s rotate infinite;
    }
    .loader span {
        display: block;
        width: 100%;
        text-align: center;
        position: absolute;
        bottom: 0;
    }
    .text_box{
        overflow: hidden;
    }
    .nav-logo{
        position: relative;
        display: block;
        pointer-events: none;
        transform: translate3d(0px, 100%, 0px);
        animation: 1s textShow alternate forwards;
    }
    .app_hide{
        overflow: hidden;
        height: 500px;
    }
    @keyframes textShow{
        0% {
            transform: translate3d(0px, 100%, 0px);
        }
        50% {
            opacity: 1;
        }
        100% {
            transform: translate3d(0px, 0%, 0px);
        }
    }
    @keyframes rotate{
        0% {
            transform: rotate(90deg);
        }
        10% {
            opacity: 0;
        }
        35% {
            transform: rotate(0deg);
            opacity: 1;
        }
        65% {
            transform: rotate(0deg);
            opacity: 1;
        }
        80% {
            opacity: 0;
        }
        100% {
            transform: rotate(-90deg);
        }
    }
</style>

    <div class="loading" id='loading'>
        <div class="loader">
            <div class="image">
                <i class="fa fa-codepen"></i>
            </div>
            <span></span>
        </div>
        <div class='load_content'>
            <div class='load_title' id='load_title'>
                <adiv id="nav-logo-w" class="_ch _f" href="/">
                    <div class='text_box'>
                        <span class="nav-logo">Analysis</span>
                    </div>
                    <div class='text_box'>
                        <span class="nav-logo">System</span>
                    </div>
                </adiv>
            </div>
            <div id='load_body'>

                <div id="loader-0">Loaded</div>
                <div id="loader-1" data-progress=0>0%</div>
                <div id="loader-line">_</div>
            </div>
        </div>
    </div>
    <!-- /.row -->


    <!-- page script -->
    <script src='/adminLte_componets/jquery/dist/jquery.js'></script>
    <script>


                var counter = 0;
                $('.loader .image').addClass('rotate_animation');
                // Start the changing images
                setInterval(function() {
                    if(counter == 9) {
                        counter = 0;
                    }
                    changeImage(counter);
                    counter++;
                }, 1000);

                // Set the percentage off
                loading();

            window.onload=loadDone;
            function loadDone(){
                $('#loader-1').data('progress',100);
                $('#loader-1').html('100%');
                setTimeout(function(){
                    $('#loading').fadeOut();
                    $('#app').removeClass('app_hide');
                }, 1500);
            }

            function changeImage(counter) {
                var images = [
                    '<i class="fa fa-fighter-jet"></i>',
                    '<i class="fa fa-gamepad"></i>',
                    '<i class="fa fa-headphones"></i>',
                    '<i class="fa fa-cubes"></i>',
                    '<i class="fa fa-paw"></i>',
                    '<i class="fa fa-rocket"></i>',
                    '<i class="fa fa-ticket"></i>',
                    '<i class="fa fa-pie-chart"></i>',
                    '<i class="fa fa-codepen"></i>'
                ];

                $(".loader .image").html(""+ images[counter] +"");
            }

            function loading(){
                var num = $('#loader-1').data('progress');

                for(i=0; i<=100; i++ ) {

                    setTimeout(function() {
                        // $('.loader span').html(num+'%');
                       let done = $('#loader-1').data('progress');
                        $('#loader-1').data('progress',num);
                        $('#loader-1').html(num+'%');

                        if(done == 100) {
                            $('#loader-1').html('100%');
                            $('#loader-1').data('progress',100);
                            return;
                        }
                        if(num < 90){
                            num++;
                        }

                    },i*10);
                };

            }
    </script>

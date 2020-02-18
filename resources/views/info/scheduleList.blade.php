<?php
    /**
     * Created by PhpStorm.
     * User: shaun
     * Date: 2020/2/17
     * Time: 下午3:05
     */
?>
@extends('layout')

@section('title','DAC | title')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        系統排程表
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">title</li>
    </ol>
@endsection

@extends('headerbar')


<!-- Content Wrapper. Contains page content -->
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">月份排程與作業時間表</h3>
                </div>
                <div class="box-body no-padding">
                    <div id="calendar"></div>
                </div>
                <div class="box-footer">
                    <h4>QA:</h4>
                    <span>1.匯率輸入如未再16號前輸入,他國幣別獎金計算,將會採用預設值 usd: 31 jpy:0.2875 將會造成資料金額浮動不準確,<storge style='color:red;'>須注意在時間內完成輸入</storge></span></br>
                    <span>2.獎金發放作業 在每月4號以前發放皆為 當月,5號開始發放將為次月 <storge style='color:blue;'>例： 1.5號 發放獎金 獎金發放檢視 查詢該筆資料 將會在2月, 1.4號 發放獎金 獎金發放檢視 查詢該筆資料 將會在1月</storge></span></br>
                    <span>3.召集人團隊資料與個人責任額資料 每月系統會自動新增,任何責任額調整、團隊人員異動,需要在下月資料產生前完成異動</span></br>
                    <span>4.每月16號 計算前月獎金與鎖定財報資料,代表未鎖定的月份 目前業績查詢的金額數字都會動態的,直到16完成計算後,不應再有任何異動</span></br>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">每日系統排程表</h3>
                </div>
                <div class="box-body no-padding">
                    <div id="schedule"></div>
                </div>
                <div class="box-footer">
                    <h4>QA:</h4>
                    <span>1.每日連動後台 收款更新資料為00:00, 任何收款資料的輸入,將在次日顯示在業績系統上</span></br>
                    <span>2.每日連動後台 財報更新為10點、15點</span></br>
                    <span>3.任何資料更新,業績系統將會在下次快取更新後顯示,更新為11點、16點</span></br>

                </div>
            </div>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
@endsection

@section('script')

    <!-- page script -->
    <script>
        var date = new Date()
        var d    = date.getDate(),
            m    = date.getMonth(),
            y    = date.getFullYear()

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                height: 650,
                plugins: [ 'dayGrid', 'timeGrid', 'list' ],
                fixedWeekCount: false,
                events    : [
                    {
                        title          : '新增本月責任額資料',
                        start          : new Date(y, m, 1),
                        allDay         : true,
                        backgroundColor: '#f56954', //red
                        borderColor    : '#f56954' //red
                    },
                    {
                        title          : '新增本月召集人資料',
                        start          : new Date(y, m, 1),
                        allDay         : true,
                        backgroundColor: '#f56954', //red
                        borderColor    : '#f56954' //red
                    },
                    {
                        title          : '鎖定前月計算財報資料',
                        start          : new Date(y, m, 16),
                        allDay         : true,
                        backgroundColor: '#f39c12', //yellow
                        borderColor    : '#f39c12' //yellow
                    },
                    {
                        title          : '計算前月獎金',
                        start          : new Date(y, m, 16),
                        allDay         : true,
                        backgroundColor: '#f39c12', //yellow
                        borderColor    : '#f39c12' //yellow
                    },
                    {
                        title          : '前月匯率輸入',
                        start          : new Date(y, m, 1),
                        end            : new Date(y, m, 16),
                        allDay         : true,
                        backgroundColor: '#0073b7', //Blue
                        borderColor    : '#0073b7' //Blue
                    },
                    {
                        title          : '發放本月獎金',
                        start          : new Date(y, m, 1),
                        end            : new Date(y, m, 5),
                        allDay         : true,
                        backgroundColor: '#00a65a', //Success (green)
                        borderColor    : '#00a65a' //Success (green)
                    }
                ],
            });

            calendar.render();
        });
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('schedule');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: [ 'interaction', 'resourceTimeline' ],
                timeZone: 'Asia/taipei',
                defaultView: 'resourceTimelineDay',
                aspectRatio: 1.5,
                height: 320,
                header: {
                    right: 'resourceTimelineDay'
                },
                resources: {!!  json_encode($timeLine['resource']) !!},
                events: {!!  json_encode($timeLine['events']) !!},
            });
            calendar.render();
        });
        $(function () {
            $('.fc-license-message').remove();
            $('.fc-resource-area.fc-widget-header').width('10%');
            $('.fc-scroller').scrollLeft(0);
        })
    </script>
@endsection

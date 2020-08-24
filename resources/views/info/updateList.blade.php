<?php
    /**
     * Created by PhpStorm.
     * User: shaun
     * Date: 2020/2/17
     * Time: 下午3:05
     */
?>
@extends('layout')

@section('title','DAC | 系統更新')

{{--導航 麵包屑--}}
@section('content-header')
    <h1>
        系統更新
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
            {{--start add-new-articles start--}}
            <div class="modal fade" id="add-new-articles">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Add New Articles</h4>
                        </div>
                        <div class="modal-body">
                            <form role="form" action='./articlesPost' method='post'>
                                <!-- text input -->
                                @csrf
                                <div class="form-group">
                                    <label>Title</label>
                                    <input name="title" type="text" class="form-control" placeholder="title ...">
                                </div>
                                <textarea id="editor" name="description" rows="10" cols="80">

                                </textarea>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            {{--end add-new-articles start--}}
            <div class="box-header">
                @if(auth()->user()->hasPermission('info.articlesPost') || auth()->user()->isAdmin())
                    <div class='col-md-2 pull-right'>
                        <button type="button" id='addNewArticles' data-toggle="modal" data-target="#add-new-articles" class="btn btn-block btn-success">新增</button>
                    </div>
                @endif
            </div>
            <ul class="timeline">
                <!-- timeline time label -->
                @foreach($articles as $article)

                <li class="time-label">
                    <span class="bg-red">
                        {{$article->created_at->format('Y/m/d')}}
                    </span>
                </li>
                <!-- /.timeline-label -->

                <!-- timeline item -->
                <li>
                    <!-- timeline icon -->
                    <i class="fa fa-envelope bg-blue"></i>
                    <div class="timeline-item">
                        <span class="time">{{$article->user->name}}<i class="fa fa-clock-o"></i>{{$article->created_at->format('H:i:s')}}</span>

                        <h3 class="timeline-header"><a href="#">{{$article->title}}</a></h3>

                        <div class="timeline-body">
                            {!! $article->description !!}
                        </div>
                        @if(auth()->user()->hasPermission('info.articlesPost') || auth()->user()->isAdmin())
                            <div class="timeline-footer">
                                <a class="btn btn-primary btn-xs" data-action='edit' data-id='{{$article->id}}' data-toggle="modal" data-target="#edit-articles-{{$article->id}}" >Edit</a>
                            </div>
                        @endif
                    </div>
                </li>
                <!-- END timeline item -->
                    {{--start edit-new-articles start--}}
                    <div class="modal fade" id="edit-articles-{{$article->id}}">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Edit Articles</h4>
                                </div>
                                <div class="modal-body">
                                    <form role="form" action='./articlesPost' method='post'>
                                        <!-- text input -->
                                        @csrf
                                        <input type='hidden' name='id' value='{{$article->id}}'>
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input name="title" type="text" class="form-control" value='{{$article->title}}' placeholder="title ...">
                                        </div>
                                        <textarea id="editor{{$article->id}}" name="description" rows="10" cols="80">
                                                {!! $article->description !!}
                                        </textarea>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                    {{--end edit-new-articles start--}}
                @endforeach
                <li>
                    <i class="fa fa-clock-o bg-gray"></i>
                </li>
            </ul>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
@endsection

@section('script')

    <!-- page script -->
    <script>
        $(function () {
            CKEDITOR.replace('editor');

            $('.row').on('click','.btn',function(){
                if( $(this).data('action') !== undefined){
                    let btnAction = $(this).data('action');

                    switch(btnAction){
                        case 'edit':
                            CKEDITOR.replace('editor'+$(this).data('id'));
                        break;
                    }
                };
            });
        })
    </script>
@endsection

<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-17
	 * Time: 16:05
	 */

?>
@section('sidebar')
    <aside class='main-sidebar'>
        <!-- sidebar: style can be found in sidebar.less -->
        <section class='sidebar'>
            <!-- Sidebar user panel -->
            <div class='user-panel'>
                <div class='pull-left image'>
                    <img src='/img/avatar5.png' class='img-circle' alt='User Image'>
                </div>
                <div class='pull-left info'>
                    <p>Alexander Pierce</p>
{{--                    <a href='/#'><i class='fa fa-circle text-success'></i> Online</a>--}}
                </div>
            </div>
            <!-- search form -->
{{--            <form action='#' method='get' class='sidebar-form'>--}}
{{--                <div class='input-group'>--}}
{{--                    <input type='text' name='q' class='form-control' placeholder='Search...'>--}}
{{--                    <span class='input-group-btn'>--}}
{{--                            <button type='submit' name='search' id='search-btn' class='btn btn-flat'>--}}
{{--                              <i class='fa fa-search'></i>--}}
{{--                            </button>--}}
{{--                          </span>--}}
{{--                </div>--}}
{{--            </form>--}}
            <!-- /.search form -->
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class='sidebar-menu' data-widget='tree'>
                <li class='header'>主選單</li>
                <menu-component :item='item' v-for='item in {{ json_encode($data['menus']) }}' :user_id='{{session('userData')['user']['id']}}'></menu-component>
            </ul>
        </section>
    </aside>
@endsection

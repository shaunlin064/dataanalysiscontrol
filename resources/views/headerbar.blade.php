<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-18
	 * Time: 13:57
	 */
?>

@section('headerbar')
	<header class='main-header'>
	<!-- Logo -->
	<a href='/#' class='logo'>
		<!-- mini logo for sidebar mini 50x50 pixels -->
		<span class='logo-mini'><b>D</b>AC</span>
		<!-- logo for regular state and mobile devices -->
		<span class='logo-lg'>Data<b>Analysis</b>Control</span>
	</a>

	<!-- Header Navbar: style can be found in header.less -->
	<nav class='navbar navbar-static-top'>
		<!-- Sidebar toggle button-->
		<a href='/#' class='sidebar-toggle' data-toggle='push-menu' role='button'>
			<span class='sr-only'>Toggle navigation</span>
		</a>
		<!-- Navbar Right Menu -->
		<div class='navbar-custom-menu'>
			<ul class='nav navbar-nav'>
				<!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown notifications-menu open">
                    <a href="#" id='start' class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                        <i class="fa fa-star-half-o">導覽</i>
                        <span id="guided_number" class="label label-warning"></span>
                    </a>
                </li>
				<li class='dropdown user user-menu'>
					<a href='/#' class='dropdown-toggle' data-toggle='dropdown'>
						<div class='user-image customer-font-1e'>
							<i class='fa fa fa-user-circle'></i>
						</div>
{{--						<img src='/img/avatar5.png' class='user-image' alt='User Image'>--}}
						<span class='hidden-xs'>{{session('userData')['name']}}</span>
					</a>
					<ul class='dropdown-menu'>
						<!-- User image -->
						<li class='user-header'>
							<div class='user-circle customer-font-6e'>
								<i class='fa fa fa-user-circle block'></i>
							</div>
{{--							<img src='/img/avatar5.png' class='img-circle' alt='User Image'>--}}

							<p>
								{{session('userData')['name']}} - {{session('userData')['department']}}
{{--								<small>Member since Nov. 2012</small>--}}
							</p>
						</li>
						<!-- Menu Body -->
{{--						<li class='user-body'>--}}
{{--							<div class='row'>--}}
{{--								<div class='col-xs-4 text-center'>--}}
{{--									<a href='/#'>Followers</a>--}}
{{--								</div>--}}
{{--								<div class='col-xs-4 text-center'>--}}
{{--									<a href='/#'>Sales</a>--}}
{{--								</div>--}}
{{--								<div class='col-xs-4 text-center'>--}}
{{--									<a href='/#'>Friends</a>--}}
{{--								</div>--}}
{{--							</div>--}}
							<!-- /.row -->
{{--						</li>--}}
						<!-- Menu Footer-->
						<li class='user-footer'>
							<div class='pull-left'>
								<a href='/#' class='btn btn-default btn-flat'>業績管理</a>
							</div>
                            @can('system.index')
							<div class='pull-left pl-5'>
								<a href='/system/menu/list' class='btn btn-default btn-flat'>系統管理</a>
							</div>
                            @endcan
							<div class='pull-right'>
								<a href='/logout' class='btn btn-default btn-flat'>登出</a>
							</div>
						</li>
					</ul>
				</li>
				<!-- Control Sidebar Toggle Button -->
{{--				<li>--}}
{{--					<a href='/#' data-toggle='control-sidebar'><i class='fa fa-gears'></i></a>--}}
{{--				</li>--}}
			</ul>
		</div>

	</nav>
</header>
@endsection

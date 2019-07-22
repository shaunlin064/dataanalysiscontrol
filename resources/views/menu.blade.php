<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-17
	 * Time: 16:05
	 */


?>
	@foreach(session('menus') as $menu)
			<li class="treeview">
			<a href="#">
				<i class='{{$menu['icon'] }}'></i>
				 <span>{{$menu['name']}}</span>
				 <span class="pull-right-container">
					<i class="fa fa-angle-left pull-right"></i>
				 </span>
			</a>
			@if(count($menu->subMenu))
				<ul class="treeview-menu {{ $menu['newclass'] }}">
					@foreach ($menu->subMenu as $sub_menu )
						@if( session('userRoleType') == 'admin'
						||
						in_array( $sub_menu->id , session('role')[session('userRoleType')]['purview']['menu_subs'] ) )
						<li class={{count($sub_menu->level2) ? 'treeview' : ''}}>
							<a href="{{ Route($sub_menu->url) }}" target="{{$sub_menu['target']}}"
							   title="{{$sub_menu['title']}}">
								<i class='{{$sub_menu['icon'] }}'></i> {{ $sub_menu['name'] }}
								@if(count($sub_menu->level2))
									<span class="pull-right-container">
										<i class="fa fa-angle-left pull-right"></i>
									</span>
								@endif
							</a>
							@if(count($sub_menu->level2))
								<ul class="treeview-menu">
									@foreach ($sub_menu->level2 as $level2 )
										@if( session('userRoleType') == 'admin'
						||
						in_array( $level2->id , session('role')[session('userRoleType')]['purview']['menu_sub_level2s'] ) )
										<li>
											<a href="{{$level2['url']}}" title="{{$level2['title']}}"
											   target="{{$level2['target']}}">
												{!! $level2['icon'] . $level2['name'] !!}
											</a>
										</li>
										@endif
									@endforeach
								</ul>
							@endif
						</li>
						@endif
					@endforeach
				</ul>
			@endif
		</li>

	@endforeach

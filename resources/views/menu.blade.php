<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-17
	 * Time: 16:05
	 */

?>
	@foreach(session('menus') as $menu)
        @if( auth()->user()->menuCheck($menu) )
            <li class="treeview {{ $menu['new_class'] }}">
            <a href="#">
                <i class='{{$menu['icon'] }}'></i>
                 <span>{{$menu['name']}}</span>
                 <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                 </span>
            </a>
			@if(count($menu->subMenu))
				<ul class="treeview-menu">
					@foreach ($menu->subMenu as $sub_menu )
                        @if(auth()->user()->hasPermission($sub_menu->url))
						<li class={{count($sub_menu->level2) ? 'treeview' : ($sub_menu->url == Route::currentRouteName() ? 'active' : '')}}>
							<a {{Route(Route::currentRouteName()) == Route($sub_menu->url) ? "class=text ":''}} href="{{ Route($sub_menu->url) }}" target="{{$sub_menu['target']}}"
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
										@if( auth()->user()->hasPermission($level2->url))
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
		@endif
	@endforeach


<header class=" header" role="header">

	<div class="top-header">


		@if( isset( $is_user_logged ) && $is_user_logged )

			<div class="u-pull-right">

				@include('menu')

				@include('dashboard.notifications_counter')

				@include('avatar.avatar', ['user_name' => $current_user_name, 'image' => $current_user_avatar])

			</div>

		@elseif(isset( $is_frontpage ) && !$is_frontpage)

			<div class="u-pull-right">

				<a href="{{ url('auth/login') }}">Login</a>

			</div>

		@endif

		<div class="logo">
			<a href="@if(isset( $is_user_logged ) && $is_user_logged){{$current_user_home_route}}@else{{route('frontpage')}}/@endif">
				&nbsp;
			</a>
		</div>
		
@if( isset( $show_search ) && $show_search)		
		@include('search.searchform')
@endif

		

		


	</div>

	<div class="sub-header">

		<div class="parent-navigation">
			@yield('sub-header')
		</div>

		<div class="u-pull-right actions">
			<div id="action-bar">

				<div class="separator"></div>

				@yield('action-menu')

			</div>
		</div>

	</div>
	
	@include('dashboard.terms_notice')

</header>
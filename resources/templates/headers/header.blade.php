
<header class=" header" role="header">

	<!--[if lt IE 8]>
	<div class="outdated outdated--visible outdated--nosupport js-outdated-ie7">

		<div>
			<img src="{{ url('/') }}/android-chrome-72x72.png" alt="K-Link logo">
		</div>

		<h1>
			{{ trans('errors.oldbrowser.nosupport') }}
		</h1>
		
		@include('static.partials.browserupdate')
	
	</div>
	<![endif]-->

	<!--[if IE 8]>
	<div class="outdated outdated--visible js-outdated-ie8">

		<span class="outdated__message">
			{{ trans('errors.oldbrowser.ie8') }}
		</span>
		
		<a href="{{route('browserupdate')}}" class="outdated__link">{{ trans('errors.oldbrowser.more_info') }}</a>
	
	</div>
	<![endif]-->

	<div class="outdated js-outdated">
	
		<span class="outdated__message">
			{{ trans('errors.oldbrowser.generic') }}
		</span>
		
		<a href="{{route('browserupdate')}}" class="outdated__link">{{ trans('errors.oldbrowser.more_info') }}</a>

	</div>

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
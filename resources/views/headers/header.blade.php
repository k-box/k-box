
<header class=" header" role="header">

	<div class="header__main">
		
		<div class="header__branding">
		
			<a class="logo" href="@if(isset( $is_user_logged ) && $is_user_logged){{$current_user_home_route}}@else{{route('frontpage')}}/@endif">
				@include('headers.logo')
			</a>
			
			@if( isset( $show_search ) && $show_search)		
				@include('search.searchform')
			@endif

		</div>

		@if( auth()->check() )

			<div class="header__navigation">

				@includeWhen((!isset($hide_menu) || (isset($hide_menu) && !$hide_menu)),'menu')

				@include('dashboard.notifications_counter')

				<div class="header__profile">
					@component('avatar.avatar', [
						'name' => $current_user_name, 
						'image' => $current_user_avatar,
						'url' => $profile_url ?? route('profile.index'),
						'alt' => trans('profile.go_to_profile')
						])

					@endcomponent

					@materialicon('navigation', 'arrow_drop_down', 'header__profile__arrow')
				
					<div class="header__profile__card">
						<div class="header__profile__name">{{$current_user_name}}</div>
						<div>
						<button class="button" onclick="event.preventDefault();document.getElementById('logout-form').submit();">{{trans('auth.logout')}}</button>
						<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
							{{ csrf_field() }}
						</form>
						</div>
					</div>
				</div>


			</div>

		@endif


	</div>

	@section('header-secondary')
		<div class="header__secondary">

			<button class="drawer__button action__button js-drawer-trigger">
				@materialicon('navigation', 'menu', 'ico')
			</button>

			<div class="breadcrumbs">
				@yield('breadcrumbs')
			</div>

			<div class="actions js-drawer-action-bar" id="action-bar">
				@yield('action-menu')
			</div>

		</div>
	@endsection

	@yield('header-secondary')

</header>

<div class="c-message c-message--warning outdated js-outdated" id="js-outdated">
	
	<button class="c-message__dismiss button button--ghost" id="js-outdated-dismiss">
		@materialicon('navigation', 'close')
	</button>

	<span class="outdated__message">
		{{ trans('errors.oldbrowser.generic') }}
		<a href="{{route('browserupdate')}}" class="button">{{ trans('errors.oldbrowser.more_info') }}</a>
	</span>

</div>


<header class="header z-10 sticky top-0 shadow-md js-header" role="header">

	<div class="relative h-header flex items-center justify-between px-2  lg:px-4 py-1 bg-gray-100">
		
		<div class="flex items-center flex-grow max-w-lg">
		
			<x-logo class="mr-4" />
			
			@if( isset( $show_search ) && $show_search)		
				@include('search.searchform')
			@endif

		</div>

		@if( auth()->check() )

			<div class="flex items-center">

				@includeWhen((!isset($hide_menu) || (isset($hide_menu) && !$hide_menu)),'menu')

				@include('headers.help')

				@include('dashboard.notifications_counter')

				@component('components.dropdown')

					@component('avatar.avatar', [
						'name' => $current_user_name, 
						'image' => $current_user_avatar,
						'url' => null,
						'alt' => trans('profile.go_to_profile')
						])

					@endcomponent

					@materialicon('navigation', 'arrow_drop_down', ['class' => 'inline fill-current arrow', ':class' => "{ 'rotate-180': open }"])

					@slot('panel')

						<div class="mb-4">
							<x-dropdown-link-item :label="$current_user_name" href="{{ $profile_url ?? route('profile.index') }}" />
						</div>
						<ul class="">
							<li>
								<x-dropdown-link-item label="{{trans('profile.go_to_profile')}}" href="{{ $profile_url ?? route('profile.index') }}" />
							</li>
							<li>
								<x-dropdown-link-item label="{{trans('auth.logout')}}" href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();" />
								<form class="hidden" id="logout-form" action="{{ route('logout') }}" method="POST">
									{{ csrf_field() }}
								</form>
							</li>
						</ul>
						
					@endslot
					
				@endcomponent


			</div>

		@endif


	</div>

	

	@yield('header-secondary')

</header>

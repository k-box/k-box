
<header class="header sticky top-0 shadow" role="header">

	<div class="relative h-12 flex items-center justify-between px-2  lg:px-4 py-1 bg-gray-200">
		
		<div class="flex items-center flex-grow max-w-lg">
		
			<a class="logo text-gray-700 hover:text-blue-600 mr-4" href="@if(isset( $is_user_logged ) && $is_user_logged){{$current_user_home_route}}@else{{route('frontpage')}}/@endif">
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

				<div class="">
					<button type="button" class="inline-flex hover:text-blue-600 items-center js-profile-link ">
						@component('avatar.avatar', [
							'name' => $current_user_name, 
							'image' => $current_user_avatar,
							'url' => null,
							'alt' => trans('profile.go_to_profile')
							])
	
						@endcomponent
	
						@materialicon('navigation', 'arrow_drop_down', 'inline fill-current js-profile-arrow')
					</button>
				
					<div class="absolute shadow hidden js-profile w-full sm:w-56 right-0 block p-2 mt-1 text-white bg-gray-700 rounded">

						<div class="mb-4">
							<a class="text-white py-2 inline-block" href="{{ $profile_url ?? route('profile.index') }}">{{$current_user_name}}</a>
						</div>
						<div>
							<a class="button w-full mb-2" href="{{ $profile_url ?? route('profile.index') }}">{{trans('profile.go_to_profile')}}</a>
							<button class="button w-full text-left" onclick="event.preventDefault();document.getElementById('logout-form').submit();">{{trans('auth.logout')}}</button>
							<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
								{{ csrf_field() }}
							</form>
						</div>
					</div>

				</div>


			</div>

		@endif


	</div>

	

	@yield('header-secondary')

</header>


@push('js')

<script>

	require(['modules/profilemenu'], function(ProfileMenu){

	});
</script>

	
@endpush
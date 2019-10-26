
<header class="header sticky top-0 shadow js-header" role="header">

	<div class="relative h-12 flex items-center justify-between px-2  lg:px-4 py-1 bg-gray-200">
		
		<div class="flex items-center flex-grow max-w-lg">
		
			<a class="logo text-gray-700 hover:text-blue-600 mr-4 hidden md:inline-block" href="@if(isset( $is_user_logged ) && $is_user_logged){{$current_user_home_route}}@else{{route('frontpage')}}/@endif">
				@include('headers.logo')
			</a>
			
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

					@materialicon('navigation', 'arrow_drop_down', 'inline fill-current arrow')

					@slot('panel')

						<div class="mb-4">
							<a class="no-underline font-bold block p-2 -mx-2 mb-1 text-black hover:bg-gray-300 active:bg-gray-400 focus:bg-gray-400 focus:outline-none" href="{{ $profile_url ?? route('profile.index') }}">{{$current_user_name}}</a>
						</div>
						<ul class="">
							<li><a class="no-underline block p-2 -mx-2 mb-1 text-black hover:bg-gray-300 active:bg-gray-400 focus:bg-gray-400 focus:outline-none" href="{{ $profile_url ?? route('profile.index') }}">{{trans('profile.go_to_profile')}}</a></li>							
							<li>
								<a href="#" class="no-underline block p-2 -mx-2 mb-1 text-black hover:bg-gray-300 active:bg-gray-400 focus:bg-gray-400 focus:outline-none" onclick="event.preventDefault();document.getElementById('logout-form').submit();">{{trans('auth.logout')}}</a>
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


@push('js')

<script>

	require(['modules/dropdown'], function(Dropdown){
		Dropdown.find(".js-header");
	});
</script>

	
@endpush
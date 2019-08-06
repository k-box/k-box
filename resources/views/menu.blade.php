<nav class="navigation navigation--primary">
	
	@if(isset($show_admin_link) && $show_admin_link)
		
		<a href="{{ route('administration.index') }}" class="navigation__item navigation__item--link @if(\Request::is('administration*'))navigation__item--current @endif">
			{{trans('administration.page_title')}}
		</a>
	
	@endif

</nav>
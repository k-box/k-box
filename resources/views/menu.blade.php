<nav class="navigation navigation--primary mr-4">
	
	@if(isset($show_admin_link) && $show_admin_link)
		
		<a href="{{ route('administration.index') }}" title="{{trans('administration.page_title')}}" class="text-black hover:text-blue-600 inline-flex items-center p-2 @if(\Request::is('administration*'))navigation__item--current @endif">
			
			@materialicon('action', 'settings', 'inline-block mr-1 fill-current opacity-75')
			
			<span class="hidden md:inline">{{trans('administration.page_title')}}</span>
		</a>
	
	@endif

</nav>
<nav class="navigation navigation--primary">
	
	{{-- @if(isset($show_search_link) && $show_search_link)
	
		<a href="{{ route('search') }}" class="navigation__item navigation__item--link @if(\Request::is('search*'))navigation__item--current @endif">
			{{trans('search.page_title')}}
		</a>
	
	@endif
	
	@if(isset($show_doc_link) && $show_doc_link)
		
		<a href="{{ route('documents.index') }}" class="navigation__item navigation__item--link @if(\Request::is('document*'))navigation__item--current @endif">
			{{trans('documents.page_title')}}
		</a>
	
	@endif
	
	@if(isset($show_projects_link) && $show_projects_link)
		
		<a href="{{ route('projects.index') }}" class="navigation__item navigation__item--link @if(\Request::is('projects*'))navigation__item--current @endif">
			{{trans('projects.page_title')}}
		</a>
	
	@endif
	
	@if(isset($show_doc_link) && isset($show_search_link) && $show_search_link && !$show_doc_link)
		
		<a href="{{ route('documents.starred.index') }}" class="navigation__item navigation__item--link @if(\Request::is('document*'))navigation__item--current @endif">
			{{trans('starred.page_title')}}
		</a>
	
	@endif
	
	@if(isset($show_groups_link) && $show_groups_link)
		
		<a href="{{ route('people.index') }}" class="navigation__item navigation__item--link @if(\Request::is('people*'))navigation__item--current @endif">
			{{trans('groups.people.page_title')}}
		</a>
	
	@endif --}}
	
	@if(isset($show_admin_link) && $show_admin_link)
		
		<a href="{{ route('administration.index') }}" class="navigation__item navigation__item--link @if(\Request::is('administration*'))navigation__item--current @endif">
			{{trans('administration.page_title')}}
		</a>
	
	@endif

</nav>
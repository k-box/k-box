<nav class="navigation primary">
				
	<ul class="menu" role="navigation">
		@if(isset($show_search_link) && $show_search_link)
		<li class="menu-item @if(\Request::is('search*'))current-item @endif">
			<a href="{{ route('search') }}">
				{{trans('search.page_title')}}
			</a>
		</li>
		@endif
		
		@if(isset($show_doc_link) && $show_doc_link)
		<li class="menu-item @if(\Request::is('document*'))current-item @endif">
			<a href="{{ route('documents.index') }}">
				{{trans('documents.page_title')}}
			</a>
		</li>
		@endif
		
		@if(isset($show_projects_link) && $show_projects_link)
		<li class="menu-item @if(\Request::is('projects*'))current-item @endif">
			<a href="{{ route('projects.index') }}">
				{{trans('projects.page_title')}}
			</a>
		</li>
		@endif
		
		@if(isset($show_doc_link) && isset($show_search_link) && $show_search_link && !$show_doc_link)
		<li class="menu-item @if(\Request::is('document*'))current-item @endif">
			<a href="{{ route('documents.starred.index') }}">
				{{trans('starred.page_title')}}
			</a>
		</li>
		@endif
		
		@if(isset($show_groups_link) && $show_groups_link)
		<li class="menu-item @if(\Request::is('people*'))current-item @endif">
			<a href="{{ route('people.index') }}">
				{{trans('groups.people.page_title')}}
			</a>
		</li>
		@endif
		
		@if(isset($show_shared_link) && $show_shared_link)
		<li class="menu-item @if(\Request::is('people*'))current-item @endif">
			<a href="{{ route('shares.index') }}">
				{{trans('share.page_title')}}
			</a>
		</li>
		@endif
		
		@if(isset($show_admin_link) && $show_admin_link)
		<li class="menu-item @if(\Request::is('administration*'))current-item @endif">
			<a href="{{ route('administration.index') }}">
				{{trans('administration.page_title')}}
			</a>
		</li>
		@endif

	</ul>

</nav>
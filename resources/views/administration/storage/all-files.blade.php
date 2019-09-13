@extends('administration.layout')

@section('breadcrumbs')
        
	<a href="{{route('administration.index')}}"  class="breadcrumb__item">{{trans('administration.page_title')}}</a> 
	<a href="{{route('administration.storage.index')}}"  class="breadcrumb__item">{{trans('administration.menu.storage')}}</a> 
	<span class="breadcrumb__item--current">{{trans('administration.storage.all_files')}}</span>

@stop



@section('page-action')

@stop

@section('page')

<div id="documents-list">
<div id="document-area">

	@include('documents.facets')
		
	<div class="list details" >

		<div class="list__header">
			@section('list_header')
				<div class="list__column list__column--large">{{trans('documents.descriptor.name')}}</div>
				<div class="list__column list__column--hideable">{{trans('documents.descriptor.added_by')}}</div>
				<div class="list__column">{{trans('documents.descriptor.last_modified')}}</div>
				<div class="list__column list__column--hideable">{{trans('documents.descriptor.language')}}</div>
			@endsection

			@yield('list_header')
		</div>

		@include('documents.partials.listing')

	</div>
	
	@if( isset($pagination) && !is_null($pagination) )
		<div class="pagination-container">

			{!! $pagination->render() !!}

		</div>
	@endif

</div>
</div>

@stop

@section('panels')

@include('panels.generic')

@stop

@section('scripts')

	<script>

	require(['modules/documents', 'modules/panels'], function(Documents, Panels){

		@if(isset($context))
			Documents.setContext({
				filter:'{{$context}}',
				maxUploadSize: {{ ceil(\KBox\Upload::maximumAsKB()) }},
				network_name: '{{ network_name() }}',
				@if(isset($context_group)) group: '{{$context_group}}', @endif
				@if(isset($current_visibility)) visibility: '{{$current_visibility}}', @endif
				search: @if(isset($search_terms)) '{{$search_terms}}' @else '' @endif,
				@if(isset($facets)) facets: {!!json_encode($facets)!!}, @endif
				@if(isset($filters)) filters: {!!json_encode($filters)!!}, @endif
				isSearchRequest: {{ isset($is_search_requested) && $is_search_requested ? 'true' : 'false' }},
				canPublish: {{ isset($can_make_public) && isset($is_klink_public_enabled) && $is_klink_public_enabled && $can_make_public ? 'true' : 'false' }},
				canShare: {{ isset($can_share) && $can_share ? 'true' : 'false' }},
				userIsProjectManager: {{ auth()->check() && auth()->user()->isProjectManager() ? 'true' : 'false' }}
			});
			Documents.groups.ensureCurrentVisibility();
		@endif

		@yield('document_script_initialization')
		
	});
	</script>

@stop
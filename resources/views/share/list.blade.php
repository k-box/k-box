@extends('global')


@section('breadcrumbs')

@if(isset($shared_group))

	<a href="{{route('shares.index')}}"  class="breadcrumb__item">{{trans('share.page_title')}}</a>
	
	<span  class="breadcrumb__item">{{$shared_group->name}}</span>

@else

	<span  class="breadcrumb__item">{{trans('share.page_title')}}</span>

@endif
		


@stop

@section('page-action')



@stop

@section('content')

	
	@if(!is_null($shares))

	<div id="document-area" class="share-section shared-with-me flow-root">

	<div class="page-actions page-actions--shared-page">

        <div class="page-actions__container">

			@unless(isset($is_search_requested) && $is_search_requested)
				<span class="page-actions__label" title="{{ trans('actions.sort_by.label') }}">
					<a href="?o=a" class="button page-actions__action page-actions__action--grouped @if(isset($order) && $order==='ASC') page-actions__action--selected @endif">{{ trans('actions.sort_by.oldest_first') }}</a>
					<a href="?o=d" class="button page-actions__action page-actions__action--grouped @if(isset($order) && $order==='DESC') page-actions__action--selected @endif">{{ trans('actions.sort_by.newest_first') }}</a>
				</span>
			@endif
		</div>
	</div>

		@if(isset($facets))
		
			@include('documents.facets')
		
		@endif

		<div class="list tiles" >

			@forelse ($shares as $result)
		
				@include('documents.descriptor', ['link_route' => 'shares.group', 'item' => ($result instanceof \KBox\DocumentDescriptor) ? $result : $result->shareable, 'share_created_at' => $result->created_at, 'share_created_at_timestamp' => $result->created_at])
		
			@empty
		
				<div class="empty">
					<svg class="empty__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 3c-2.33 0-4.31 1.46-5.11 3.5h10.22c-.8-2.04-2.78-3.5-5.11-3.5z"/></svg>

					<p>{{ trans('share.empty_with_me_message') }}</p>
				</div>
		
			@endforelse

		</div>
		
		@if( isset($pagination) && !is_null($pagination) )
			<div class="pagination-container">
	
				{!! $pagination->render() !!}
	
			</div>
		@endif

	</div>
	
	@endif


@stop


@section('panels')

@include('panels.generic')

@stop

@section('scripts')

	<script>
	require(['modules/documents'], function(Documents){

		@if(isset($context))
			Documents.setContext({
				filter:'{{$context}}',
				@if(isset($context_group)) group: '{{$context_group}}', @endif
				@if(isset($current_visibility)) visibility: '{{$current_visibility}}', @endif
				search: @if(isset($search_terms)) '{{$search_terms}}' @else '' @endif,
				@if(isset($facets)) facets: {!!json_encode($facets)!!}, @endif
				@if(isset($filters)) filters: {!!json_encode($filters)!!} @endif
			});
			
		@endif

		@yield('document_script_initialization')
		
	});
	</script>

@stop
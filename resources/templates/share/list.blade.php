@extends('management-layout')


@section('sub-header')

@if(isset($shared_group))

	<a href="{{route('shares.index')}}" class="parent">{{trans('share.page_title')}}</a>
	
	{{$shared_group->name}}

@else

	{{trans('share.page_title')}}

@endif
		


@stop

@section('page-action')



@stop

@section('content')

	
	@if(!is_null($shares))

	<div id="document-area" class="share-section shared-with-me clearfix">

		@if(isset($facets))
		
			@include('documents.facets')
		
		@endif

		<div class="list tiles" >

			@forelse ($shares as $result)
		
				@include('documents.descriptor', ['link_route' => 'shares.group', 'item' => ($result instanceof \KlinkDMS\DocumentDescriptor) ? $result : $result->shareable])
		
			@empty
		
				<p>{{ trans('share.empty_with_me_message') }}</p>
		
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
	require(['modules/documents', 'modules/panels'], function(Documents, Panels){

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
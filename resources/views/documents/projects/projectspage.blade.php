@extends('documents.projects.layout')


@section('document_area')


	@if(isset($documents) && $documents->count() > 0)

		@foreach ($documents as $result)

			@include('documents.descriptor', ['item' => $result])

		@endforeach

	@elseif(isset($projects) && $projects->count() > 0)

		@foreach ($projects as $result)

			@include('documents.projects.project', ['item' => $result])

		@endforeach

	@else

	

		@if(isset($empty_message))

			<p>{!!$empty_message!!}</p>

		@elseif($is_search_requested)

			@if($search_terms==='*')

				<p>{{ trans('search.no_results_generic') }}</p>
			@else 

				<p>{{ trans('search.no_results_for_term', ['term' => $search_terms]) }}</p>
			@endif

		@else

			<p>{{ trans('projects.no_projects') }}</p>

		@endif
		

	@endif
	

@stop

@section('document_script_initialization')

	@if(isset($can_upload) && $can_upload)
		// Documents.initUploadService();
	@endif

@stop
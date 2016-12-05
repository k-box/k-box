
@extends('default-layout')


@section('action-menu')



@stop

@section('content')


	<div class="row non-map">

		@if( $search_error )

			<p>{{trans('search.error')}}</p>

		@else

		


		<div class="nine columns">

		@if(!$only_facets && empty($search_terms))
			<p class="advice">{{trans('search.empty_query')}}</p>
		@endif


		@include('documents.facets')

		

<div id="documents-list">
		<div  class="list {{$list_style_current}}">

		@forelse ($results as $result)

			@include('documents.descriptor', ['item' => $result])

		@empty

			@unless (empty($search_terms) || $only_facets)
			<p class="advice">{!!trans('search.no_results', ['term' =>$search_terms, 'collection' => network_name()])!!}</p>

			<p class="advice">{!!trans('search.try_message', [
					'startwithlink' => '<a href="'. route('search') .'?s='.$search_terms.'*&visibility='.$current_visibility.'" class="button">'.$search_terms.'&hellip;</a>'
				])!!}

			</p>

			@endunless

		@endforelse

		</div>
</div>

		@if( $total_results > 0 && !$only_facets)
			<div class="pagination-container">

				{!! $pagination->render() !!}

			</div>
		@endif
		</div>



		<div class="three columns">

			@include('widgets.search-statistics', ['results_found' => $total_results,  
				'current_visibility' => $current_visibility,
				'document_total' => $klink_indexed_documents_count])

		</div>

		@endif

	</div>

@stop

@section('panels')

@include('panels.generic')

@stop

@section('scripts')

	<script>
	require(['modules/search'], function(Search){

	});

	</script>

@stop

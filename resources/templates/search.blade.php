
@extends('default-layout')


@section('action-menu')

	@if($is_user_logged)
		@include('actions.list-switcher')
	@endif

@stop

@section('content')

	@include('map.map')


	<div class="row non-map">

		@if( $search_error )

			<p>{{trans('search.error')}}</p>

		@else

		


		<div class="nine columns">

		@if(!$only_facets && empty($search_terms))
			<p class="advice">{{trans('search.empty_query')}}</p>
		@endif


	

		<div class="elastic-list-container">
			<div id="elastic-list" class="elasticlist">
				
				{{trans('search.loading_filters')}}

			</div>
			
		</div>

		

<div id="documents-list">
		<div  class="list {{$list_style_current}}">

		@forelse ($results as $result)

			@include('documents.descriptor', ['item' => $result])

		@empty

			@unless (empty($search_terms) || $only_facets)
			<p class="advice">{!!trans('search.no_results', ['term' =>$search_terms, 'collection' => trans('documents.visibility.' . $current_visibility)])!!}</p>

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
				'search_time' => $search_time, 
				'document_total' => 1000, 
				'current_visibility' => $current_visibility,
				'document_total' => $klink_indexed_documents_count])

			@if( $is_user_logged )

				@include('widgets.recent-searches')

				@include('widgets.starred-documents')


			@endif
		</div>

		@endif

	</div>

@stop

@section('panels')

@include('panels.generic')

@stop

@section('scripts')

	<script>
	require(['modules/search', 'modules/star'@if($is_user_logged), 'modules/list-switcher' @endif ], function(Search, Star){

		@if(!$search_error)

		Search.updateElasticList(
			'#elastic-list', 
			'{{$search_terms}}',
			'{{$current_visibility}}',
			{!!json_encode($facets)!!}, 
			{!!json_encode($filters)!!},
			{
                institutionId: "{{trans('search.facets.institutionId')}}",
                language: "{{trans('search.facets.language')}}",
                documentType: "{{trans('search.facets.documentType')}}",
            }	);


		@endif

	});

	</script>

@stop

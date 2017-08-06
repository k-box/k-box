
@extends('default-layout')


@section('action-menu')

@unless( $search_error )

	<p> 
		<span class="found">{{ $total_results }}</span><span class="total"> / {{$klink_indexed_documents_count}}</span> {{ trans_choice('widgets.search_statistics.found', $total_results) }}
	</p>

@endif

@stop

@section('content')


		@if( $search_error )

			<p>{{trans('search.error')}}</p>

		@else

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
		


			

		
		@endif
@stop

@section('panels')

@include('panels.generic')

@stop

@section('scripts')

	<script>
	require(['jquery', 'modules/search'], function($, Search){

		$(document).ready(function() {
			$('#documents-list .item__thumbnail img').unveil(undefined, function() {});
		});

	});

	</script>

@stop

@extends('documents.document-layout')


@section('page-action')


@stop

@section('document_area')
			
	@include('documents.partials.listing')

@stop

@section('additional_filters')

	<button class="button" rv-on-click="openCloseMap">
		@materialicon('maps', 'map', 'button__icon'){{trans('geo::section.filters.spatial')}}
	</button>

@stop

@section('additional_filter_panels')

	<div class="elastic-list-container @if(isset($filters) && !is_null($filters)) visible @endif" rv-visible="isMapVisible">
		<div id="js-spatialfilter" class="elasticlist spatialfilter" style="width: 100%; height: 300px; position: relative;">
	
		
		
		</div>
	</div>

@stop





@section('document_script_initialization')

	@if(isset($can_upload) && $can_upload)
		Documents.initUploadService();
	@endif

	$('.dz-message').hide();

	require(['modules/spatial_filters'], function(SF){

		SF.init({
			filter: {!! $spatial_filters !!},
			otherFilters: {!! json_encode($other_filters) !!},
			searchTerms: @if($search_terms) {"s": "{{ $search_terms }}"} @else null @endif
		});

	});

@stop
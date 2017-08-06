@extends('documents.document-layout')

@section('breadcrumbs')

	
		
		<a href="{{route('documents.index')}}"  class="breadcrumb__item">{{trans('documents.page_title')}}</a> <span class="breadcrumb__item--current">{{trans('documents.create.page_breadcrumb')}}</span>



@stop


@section('action-menu')


	<div class="separator"></div>

	<div class="action-group">

		<a href="{{route('documents.import')}}" class="button">
			{{trans('actions.import')}}
		</a>

	</div>

	<div class="separator"></div>

@stop


@section('document_list_area')

@include('errors.list')

	<h3>{{trans('documents.create.page_title')}}</h3>

	<form action="{{route('documents.store')}}" enctype="multipart/form-data" method="post">
		
		<input type="hidden" name="_token" value="{{{ csrf_token() }}}"> 

		@if( $errors->has('document') )
			<span class="field-error">{{ implode(",", $errors->get('document'))  }}</span>
		@endif
		<input type="file" name="document" id="document">

		<p>
			<button type="submit">{{trans('actions.upload_alt')}}</button>
		</p>

	</form>

@stop

@section('panels')

@include('panels.generic')

@stop

@section('scripts')

	<script>
	require(['modules/panels', 'modules/list-switcher', 'modules/documents'], function(Panels, Switcher){
	});
	</script>

@stop
@extends('documents.document-layout')

@section('page-action')

	<a href="#" class="action__button" rv-disabled="nothingIsSelected" rv-on-click="restore">
		@materialicon('action','settings_backup_restore'){{trans('actions.restore')}}
	</a>
	
	<a href="#" class="action__button" rv-on-click="emptytrash">
		@materialicon('action','delete'){{trans('actions.empty_trash')}}
	</a>

@stop


@section('document_area')


	@include('documents.partials.listing')

@stop

@section('document_script_initialization')

	@if(isset($can_upload) && $can_upload)
		Documents.initUploadService();
	@endif

@stop
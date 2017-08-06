@extends('documents.document-layout')

@section('page-action')

@stop

@section('document_area')


	@include('documents.partials.listing')

@stop

@section('document_script_initialization')

	@if(isset($can_upload) && $can_upload)
		Documents.initUploadService();
	@endif

@stop
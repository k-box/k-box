@extends('documents.document-layout')


@section('page-action')


@stop

@section('document_area')
			
		@forelse ($starred as $star)

			@include('documents.descriptor', ['item' => $star->document, 'star_id' => $star->id])

		@empty

			@if(isset($empty_message))
				{{$empty_message}}
			@else
				No Starred, star a document to make something new
			@endif

		@endforelse


@stop


@section('document_script_initialization')

@stop
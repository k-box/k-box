@extends('errors.http-error')

@push('title')
{{trans('errors.404_title')}} &ndash;
@endpush

@section('message')

	@if(!isset($error_message))

		{!!trans('errors.404_text')!!}
	
	@else
	
		{!!$error_message!!}
	
	@endif
    
@stop

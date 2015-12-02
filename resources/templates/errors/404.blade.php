@extends('errors.http-error')

@section('title')

	{{trans('errors.404_title')}}

@stop

@section('content')

	@if(!isset($error_message))

		{!!trans('errors.404_text')!!}
	
	@else
	
		{{$error_message}}
	
	@endif
    
@stop

@extends('errors.http-error')

@section('title')

	{{trans('errors.klink_exception_title')}}

@stop


@section('content')

	{!!trans('errors.klink_exception_text')!!}
    
@stop
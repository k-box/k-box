@extends('errors.http-error')

@section('title')

	{{trans('errors.503_title')}}

@stop


@section('content')

	{!!trans('errors.503_text')!!}
    
@stop
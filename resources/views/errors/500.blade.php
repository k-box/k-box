@extends('errors.http-error')

@section('title')

	{{trans('errors.500_title')}}

@stop


@section('content')

	{!!trans('errors.500_text')!!}
    
@stop
@extends('errors.http-error')

@section('title')

	{{trans('errors.503-readonly_title')}}

@stop


@section('content')

	{!!trans('errors.503-readonly_text_styled')!!}
    
@stop
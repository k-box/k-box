@extends('errors.http-error')

@section('title')

	{{trans('errors.405_title')}}

@stop

@section('content')

	{!!trans('errors.405_text')!!}
    
@stop

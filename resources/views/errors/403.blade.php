@extends('errors.http-error')

@section('title')

	{{trans('errors.401_title')}}

@stop

@section('content')

	{!!trans('errors.401_text')!!}
    
@stop

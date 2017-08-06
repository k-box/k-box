@extends('errors.http-error')

@section('title')

	{{trans('errors.500_title')}}

@stop


@section('content')

	{!!trans('errors.kcore_connection_problem')!!}
    
@stop
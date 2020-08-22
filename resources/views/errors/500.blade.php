@extends('errors.http-error')

@push('title')
{{trans('errors.500_title')}} &ndash;
@endpush


@section('message')

	{!!trans('errors.500_text')!!}
    
@stop
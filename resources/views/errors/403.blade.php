@extends('errors.http-error')

@push('title')
{{trans('errors.401_title')}} &ndash;
@endpush

@section('message')

	{!!trans('errors.401_text')!!}
    
@stop

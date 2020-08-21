@extends('errors.http-error')

@push('title')
{{trans('errors.405_title')}} &ndash;
@endpush

@section('message')

	{!!trans('errors.405_text')!!}
    
@stop

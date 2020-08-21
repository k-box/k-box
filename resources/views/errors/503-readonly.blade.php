@extends('errors.http-error')

@push('title')
{{trans('errors.503-readonly_title')}} &ndash;
@endpush


@section('message')

	{!!trans('errors.503-readonly_text_styled')!!}
    
@stop
@extends('errors.http-error')

@push('title')
{{trans('errors.503_title')}} &ndash;
@endpush


@section('message')

	{!!trans('errors.503_text')!!}
    
@stop

@section('actions')
@endsection

@section('footer')
@endsection
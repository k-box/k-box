@extends('errors.http-error')

@push('title')
{{trans('errors.login_title')}} &ndash;
@endpush

@section('message')

	{!!trans('errors.login_text')!!}
    
@stop


@section('actions')
<div>
	<a class="button" target="_blank" rel="noopener noreferrer" href="{{ route('login') }}">{{ trans('auth.login') }}</a>
</div>
@endsection



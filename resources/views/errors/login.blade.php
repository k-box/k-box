@extends('errors.http-error')

@section('title')

	{{trans('errors.login_title')}}

@stop

@section('content')

	{!!trans('errors.login_text')!!}
    
@stop


@section('actions')
<div>
	<a class="button" target="_blank" noreferrer href="{{ url('/') }}">{{ trans('auth.login') }}</a>
</div>
@endsection



@extends('global')

@section('breadcrumbs')

	{{trans('pages.browserupdate')}}

@stop


@section('content')

	<div class="c-page">
		
		@include('static.partials.browserupdate')

	</div>

@stop

@section('footer')

	@include('footer')

@stop
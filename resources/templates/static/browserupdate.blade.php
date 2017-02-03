@extends('static.static-layout')

@section('sub-header')

	{{trans('pages.browserupdate')}}

@stop


@section('content')

	<div class="book">
		
		@include('static.partials.browserupdate')

	</div>

@stop
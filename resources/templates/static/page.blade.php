@extends('static.static-layout')

@section('sub-header')

	@if(isset($page_title))
		{{$page_title}}
	@elseif(isset($pagetitle))
		{{$pagetitle}}
	@endif

@stop


@section('content')

	<div class="book">
		{!!$page_content!!}
	</div>

@stop
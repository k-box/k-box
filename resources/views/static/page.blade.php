@extends('global')

@section('breadcrumbs')

	@if(isset($page_title))
		{{$page_title}}
	@elseif(isset($pagetitle))
		{{$pagetitle}}
	@endif

@stop


@section('content')

	<div class="c-page">
		{!!$page_content!!}
	</div>

@stop


@section('footer')

	@include('footer')

@stop
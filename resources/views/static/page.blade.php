@extends('global')

@section('breadcrumbs')

	@if(isset($page_title))
		{{$page_title}}
	@elseif(isset($pagetitle))
		{{$pagetitle}}
	@endif

@stop


@section('content')

    <div class="h-5"></div>

	<div class="max-w-4xl md:mx-auto px-2 lg:px-0">

		<x-markdown>{!! $page_content !!}</x-markdown>

	</div>
@stop


@section('footer')

	@include('footer')

@stop
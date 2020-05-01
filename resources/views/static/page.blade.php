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

	<div class="max-w-4xl">

		@component('components.markdown', ['class' => ''])
			{!!$page_content!!}
		@endcomponent

	</div>
@stop


@section('footer')

	@include('footer')

@stop
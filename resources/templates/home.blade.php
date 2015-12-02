
@extends('default-layout')

@section('content')

	<div class="row">


		<div class="eight columns">

			main space


		</div>

		<div class="four columns">

			@include('widgets.starred-documents', ['items' => $starred])

		</div>

	</div>

@stop

@section('scripts')

	<script>
	require(["modules/search_switcher", "modules/star"], function(SearchSwitcher, Star){
	});
	</script>

@stop

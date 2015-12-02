@extends('default-layout')




@section('action-menu')



@stop

@section('content')

<div class="row summary">
	@include('widgets.hero-counter')
</div>

@include('dashboard.notices')


<div class="row">

	<div class="four columns">

		@include('widgets.admin-shortcuts')

	</div>

	<div class="four columns">
		
		@include('widgets.recent-documents')

		@include('widgets.starred-documents')

	</div>

	<div class="four columns">

		@include('widgets.storage')

		@include('widgets.users-activity')

	</div>

	

</div>

@stop

@section('scripts')

	<script>
		require(["modules/search_switcher", "modules/star"], function(SearchSwitcher, Star){
	});
	</script>

@stop
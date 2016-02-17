@extends('management-layout')


@section('action-menu')
	

@stop


@section('sub-header')
		
    <a href="{{route('projects.show', ['id' => $project->id])}}" class="parent">{{ $project->name }}</a>

    <span >{{ trans('microsites.actions.create') }}</span>

@stop


@section('content')

<h3>{{ $pagetitle }}</h3>

<form action="{{ route('microsites.store') }}" class="microsites-form" method="POST">

    @include('sites::form')
    
    <div class="clearfix"></div>
    
    <h4>{{ trans('microsites.labels.content') }}</h4>
    <span class="description">{{ trans('microsites.hints.content') }}</span>
    
    <div class="eight columns">
        @include('sites::partials.content_form')
    </div>
    
    <!--<div class="widget four columns">
            @include('sites::partials.menu_builder')
    </div>-->

</form>




@stop





@section('panels')

@include('panels.generic')

@stop

@section('scripts')

	<script>
	// require(['modules/people'], function(People){
	
	// });
	</script>

@stop
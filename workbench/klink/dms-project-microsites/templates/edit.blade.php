@extends('global')


@section('action-menu')
	

@stop


@section('breadcrumbs')
		
    <a href="{{route('documents.projects.index')}}" class="breadcrumb__item">{{trans('projects.page_title')}}</a>

    <span class="breadcrumb__item--current">{{ $project->name }} - {{ trans('microsites.actions.edit') }}</span>

@stop


@section('content')

<h3>{{ $pagetitle }}</h3>

<form action="{{ route('microsites.update', ['id' => $microsite->id]) }}"  class="microsites-form" method="POST">
    <input type="hidden" name="_method" value="PUT" />

    @include('sites::form')
    
    <div class="clearfix"></div>
    
    <h4>{{ trans('microsites.labels.content') }}</h4>
    <span class="description">{{ trans('microsites.hints.content') }}</span>
    
    <div  class="eight columns">
        @include('sites::partials.content_form')
    </div>

</form>




@stop





@section('panels')

@include('panels.generic')

@stop

@section('scripts')

	<script>

	</script>

@stop
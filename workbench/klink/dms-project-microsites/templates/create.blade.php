@extends('global')


@section('action-menu')
	

@stop


@section('breadcrumbs')
		
    <a href="{{route('documents.projects.index')}}" class="breadcrumb__item">{{trans('projects.page_title')}}</a>

    <span class="breadcrumb__item--current">{{ $project->name }} - {{ trans('microsites.actions.create') }}</span>

@stop


@section('content')

<h3>{{ $pagetitle }}</h3>

<form action="{{ route('microsites.store') }}" class="c-form" method="POST">

    @include('sites::form')
    
    
    <h4 class="c-section__title">{{ trans('microsites.labels.content') }}</h4>
    <span class="c-section__description">{{ trans('microsites.hints.content') }}</span>
    
    <div class="c-form__field">
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
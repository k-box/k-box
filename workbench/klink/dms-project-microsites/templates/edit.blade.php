@extends('default-layout')


@section('action-menu')
	

@stop


@section('breadcrumbs')
		
    <a href="{{route('documents.projects.index')}}" class="breadcrumb__item">{{trans('projects.page_title')}}</a>

    <span class="breadcrumb__item--current">{{ $project->name }} - {{ trans('microsites.actions.edit') }}</span>

@stop


@section('content')

<div class="py-2 border-b border-gray-400 text-sm flex flex-no-wrap">   
    @yield('breadcrumbs')
</div>

<h3 class="my-4">{{ $pagetitle }}</h3>

<form action="{{ route('microsites.update', ['id' => $microsite->id]) }}"  class="microsites-form" method="POST">
    <input type="hidden" name="_method" value="PUT" />

    @include('sites::form')

    <div class=" my-8">
        <label class="font-bold">{{trans('microsites.labels.publishing_box')}}</label>
        <button type="submit" class="button button--primary">{{ trans( isset($microsite) ? 'microsites.actions.save' : 'microsites.actions.publish') }}</button>&nbsp;{{trans('actions.or_alt')}} <a href="{{ route('projects.show', ['id' => $project->id]) }}">{{trans('microsites.labels.cancel_and_back')}}</a>
    </div>
    
    <h4 class="mt-4">{{ trans('microsites.labels.content') }}</h4>
    <span class="description inline-block mb-4">{{ trans('microsites.hints.content') }}</span>
    
    @include('sites::partials.content_form')

    <div class=" mt-8">
        <label class="font-bold">{{trans('microsites.labels.publishing_box')}}</label>
        <button type="submit" class="button button--primary">{{ trans( isset($microsite) ? 'microsites.actions.save' : 'microsites.actions.publish') }}</button>&nbsp;{{trans('actions.or_alt')}} <a href="{{ route('projects.show', ['id' => $project->id]) }}">{{trans('microsites.labels.cancel_and_back')}}</a>
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
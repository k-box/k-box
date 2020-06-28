@extends('layout.full')



@section('breadcrumbs')
	<a href="{{route('documents.projects.index')}}" class="breadcrumb__item">{{trans('projects.page_title')}}</a> {{trans('projects.edit_page_title', ['name' => $project->name])}}
@stop


@section('page')


    <h3 class="my-4">{{trans('projects.edit_page_title', ['name' => $project->name])}}</h3>
	
	
	@include('errors.list')


    <form  method="post" class="js-project-form" enctype="multipart/form-data" action="{{route('projects.update', $project->id)}}">
		
		{{ method_field('PUT') }}
		
		@include('projects.partials.form', ['submit_btn' => trans('projects.labels.edit_submit')])
    
    </form>
			
			


@stop





@section('panels')

@include('panels.generic')

@stop

@section('scripts')

	<script>

	// require(['jquery'], function($){

	// 	$(".js-select-users").select2({
	// 		placeholder: "{{trans('projects.labels.users_placeholder')}}",
	// 	});

	// });

	</script>

@stop
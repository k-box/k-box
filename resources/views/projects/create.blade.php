@extends('layout.full')



@section('breadcrumbs')

	<a href="{{route('documents.projects.index')}}" class="breadcrumb__item">{{trans('projects.page_title')}}</a> {{trans('projects.create_page_title')}}

@stop


@section('page')


    <h3 class="my-4">{{trans('projects.create_page_title')}}</h3>
	
	
	@include('errors.list')


    <form  method="post" action="{{route('projects.store')}}"  enctype="multipart/form-data">
    
	@include('projects.partials.form', ['submit_btn' => trans('projects.labels.create_submit'), 'create' => true])
        
    </form>
			
			


@stop





@section('panels')

@include('panels.generic')

@stop

@section('scripts')

	<script>

	</script>

@stop
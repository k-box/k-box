@extends('management-layout')



@section('sub-header')

	

		<a href="{{route('projects.index')}}" class="parent">{{trans('projects.page_title')}}</a> {{trans('projects.edit_page_title', ['name' => $project->name])}}


@stop


@section('action-menu')
	
	
@stop



@section('content')


    <h3>{{trans('projects.edit_page_title', ['name' => $project->name])}}</h3>
	
	
	@include('errors.list')


    <form  method="post" action="{{route('projects.update', ['id' => $project->id])}}">
		
		<input type="hidden" name="_method" value="PUT">
		
		@include('projects.partials.form', ['submit_btn' => trans('projects.labels.edit_submit'), 'cancel_route' => route('projects.show', $project->id)])
    
    </form>
			
			


@stop





@section('panels')

@include('panels.generic')

@stop

@section('scripts')

	<script>
	// require(['modules/people'], function(People){
	// 	People.data({!! $groups !!}, {!! $available_users_encoded !!});
	// });
	</script>

@stop
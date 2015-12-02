@extends('projects.layout')



@section('sub-header')

	<a href="{{route('projects.index')}}" class="parent">{{trans('projects.page_title')}}</a> {{$project->name}}

@stop


@section('action-menu')

	<div class="action-group">

		<a href="{{route('projects.edit', ['id' => $project->id])}}" class="button">
			<span class="btn-icon icon-content-white icon-content-white-ic_create_white_24dp"></span>{{trans('projects.edit_button')}}
		</a>

	</div>
	
	<div class="separator"></div>
	
	<div class="action-group">

		<a href="{{route('projects.create')}}" class="button">
			<span class="btn-icon icon-content-white icon-content-white-ic_add_circle_outline_white_24dp"></span>{{trans('projects.new_button')}}
		</a>

	</div>

@stop



@section('project_area')

	<h3>{{$project->name}}</h3>
	
	<p>{{$project->description}}</p>


	<div class="row">
		
		<!--<div class="six columns">
			folders
		</div>-->
		
		<div class="six columns">
			<p>{{trans('projects.labels.users')}}</p>
			@forelse($project->users as $user)
			
				
				<span>
					<span class="btn-icon icon-social-black icon-social-black-ic_person_black_24dp"></span>
					{{$user->name}}
				</span>
			
			@empty
			
				<p class="description">{{trans('groups.people.no_users')}}</p>
				
			@endforelse
		</div>
		
		
	</div>


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
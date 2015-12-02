@extends('management-layout')



@section('sub-header')

	

		<a href="{{route('projects.index')}}" class="parent">{{trans('projects.page_title')}}</a> {{trans('projects.create_page_title')}}


@stop


@section('action-menu')
	
	<div class="action-group">

		<a href="{{route('projects.create')}}" class="button" rv-on-click="createGroup">
			<span class="btn-icon icon-content-white icon-content-white-ic_add_circle_outline_white_24dp"></span>{{trans('projects.new_button')}}
		</a>

	</div>

	<div class="separator"></div>
@stop



@section('content')


    <h3>{{trans('projects.create_page_title')}}</h3>
	
	
	@include('errors.list')


    <form  method="post" action="{{route('projects.store')}}">
    
	@include('projects.partials.form', ['submit_btn' => trans('projects.labels.create_submit')])
        
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
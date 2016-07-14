@extends('management-layout')



@section('sub-header')

	

		<a href="{{route('projects.index')}}" class="parent">{{trans('projects.page_title')}}</a> {{trans('projects.create_page_title')}}


@stop


@section('action-menu')
	
@stop



@section('content')


    <h3>{{trans('projects.create_page_title')}}</h3>
	
	
	@include('errors.list')


    <form  method="post" action="{{route('projects.store')}}">
    
	@include('projects.partials.form', ['submit_btn' => trans('projects.labels.create_submit'), 'create' => true])
        
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
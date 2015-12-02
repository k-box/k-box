@extends('management-layout')

@section('sub-header')

	{{trans('administration.page_title')}}

@stop


@section('action-menu')



@stop

@section('content')


@include('dashboard.notices')

<div class="row">

	<div class="eight columns">

		@include('administration.adminmenu')

	</div>


	<div class="four columns">

		@include('widgets.storage')

		@include('widgets.users-sessions')

	</div>

	

</div>

@stop
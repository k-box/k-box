@extends('management-layout')

@section('sub-header')

	{{trans('administration.page_title')}}

@stop


@section('action-menu')



@stop

@section('content')

<div class="row">

	<div class="eight columns">

		@include('administration.adminmenu', ['block' => true])

	</div>

	<div class="four columns widgets">

		@include('dashboard.notices')

		@include('widgets.storage')

		@include('widgets.users-sessions')

	</div>

	

</div>

@stop
@extends('global')


@section('header')

	
<header class="container header" role="header">

	<div class="row">

		<div class="twelve columns" style="text-align:center">
			
			<div class="logo white">&nbsp;</div>
			<h4 class="title">{{trans('dashboard.project_edition')}}</h4>

		</div>

	</div>


</header>

@stop


@section('footer')

@stop


@section('content')

	<div class="login-container">

	@yield('login-box')

	@include('footer', array('not_show_links' => true))

	</div>

@stop
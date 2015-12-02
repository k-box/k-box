
@extends('default-layout')


@section('content')

	<div class="row hero">
		<div class="six columns">
			
			<h1 class="hero-title">{!! trans('dashboard.welcome.hero_title')!!}</h1>

			
			
			@include('auth.loginform')

		</div>
	</div>
<!-- picture from Sergei Zolkin via Unsplash https://unsplash.com/szolkin https://images.unsplash.com/photo-1433840496881-cbd845929862?q=80&fm=jpg&s=c9a1a21dbf8a9d16477ea4b54afc8a48 -->
		


@stop

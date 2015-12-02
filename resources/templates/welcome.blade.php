
@extends('default-layout')


@section('content')

	<div class="row hero">
		<div class="seven columns">
			
			<h1 class="hero-title">{!! trans('dashboard.welcome.hero_title', ['institution' => $dms_institution_name])!!}</h1>

			<p>{!! trans('dashboard.welcome.hero_sub', ['institutions' => $dms_how_many_institutions, 'documents' => $dms_how_many_public_documents])!!}</p>

			<p>{{trans('dashboard.welcome.hero_description')}}</p>

		</div>
		<div class="five columns">
			
			@include('auth.loginform')

		</div>
	</div>

		


@stop

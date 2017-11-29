@extends('global')


@section('content')

	<div class="c-login">

		<picture class="c-login__background">

			<source media="(max-width: 640px)"
					srcset="{{asset('images/land-medium.jpg')}}">

			<source media="(max-width: 1280px)"
					srcset="{{asset('images/land-large.jpg')}}">

			<img src="{{asset('images/land.jpg')}}">

		</picture>

		<div class="c-login__credit">
			<svg xmlns="http://www.w3.org/2000/svg" style="height:12px;width:auto;margin-right:4px;position:relative;vertical-align:middle;fill:white;" viewBox="0 0 32 32"><title></title><path d="M20.8 18.1c0 2.7-2.2 4.8-4.8 4.8s-4.8-2.1-4.8-4.8c0-2.7 2.2-4.8 4.8-4.8 2.7.1 4.8 2.2 4.8 4.8zm11.2-7.4v14.9c0 2.3-1.9 4.3-4.3 4.3h-23.4c-2.4 0-4.3-1.9-4.3-4.3v-15c0-2.3 1.9-4.3 4.3-4.3h3.7l.8-2.3c.4-1.1 1.7-2 2.9-2h8.6c1.2 0 2.5.9 2.9 2l.8 2.4h3.7c2.4 0 4.3 1.9 4.3 4.3zm-8.6 7.5c0-4.1-3.3-7.5-7.5-7.5-4.1 0-7.5 3.4-7.5 7.5s3.3 7.5 7.5 7.5c4.2-.1 7.5-3.4 7.5-7.5z"></path></svg><span class="credit__text">{{ trans('dashboard.welcome.photo_by') }} Yuri Skochilov, <a href="https://ecocentre.tj/en/" target="_blank" rel="noopener noreferrer">Youth Ecological Centre</a></span>
		</div>
		<div class="c-login__spacer">
		</div>
		
		<div class="c-login__form">
			@include('auth.loginform')	
		</div>
		
	</div>
@stop
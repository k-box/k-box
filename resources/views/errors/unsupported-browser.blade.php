<!DOCTYPE html>
<html class="no-js" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>
        	@if( isset($pagetitle) ) {{ $pagetitle }} &ndash; @endif 
        	{{ config('app.name') }}
        </title>
        <meta name="description" content="The K-Box">
        <meta name="viewport" content="width=device-width, initial-scale=1">

		<link rel="stylesheet" href="{{ mix("css/vendor.css") }}">
		<link rel="stylesheet" href="{{ mix("css/app-evolution.css") }}">
                
		<meta name="token" content="{{{ csrf_token() }}}">

		<meta name="base" content="{{ url('/') }}/">
		
		<link rel="apple-touch-icon" sizes="180x180" href="{{ url('/') }}/apple-touch-icon.png">
		<link rel="mask-icon" href="{{ url('/') }}/safari-pinned-tab.svg" color="#4DBEC6">
		<link rel="icon" type="image/png" href="{{ url('/') }}/favicon-32x32.png" sizes="32x32">
		<link rel="icon" type="image/png" href="{{ url('/') }}/favicon-16x16.png" sizes="16x16">
		<link rel="manifest" href="{{ url('/') }}/manifest.json">
		<meta name="apple-mobile-web-app-title" content="K-Box">
		<meta name="application-name" content="K-Box">
		<meta name="msapplication-TileColor" content="#00aba9">
		<meta name="msapplication-TileImage" content="{{ url('/') }}/mstile-150x150.png">
		<meta name="theme-color" content="#4DBEC6">

		@include('analytics.analytics')

	</head>
	<body class="">
	
		<div class="c-message c-message--warning " style="margin-top:-96px">

			<h1 style="font-size:32px;margin-bottom:16px">
				{{ config('app.name') }}
			</h1>

			<h2 style="margin-bottom:16px;">
				{{ trans('errors.oldbrowser.nosupport') }}
			</h2>
			
			@include('static.partials.browserupdate')
		
		</div>

		@includeWhen(support_active('uservoice'), 'support.uservoice', [
			'feedback_loggedin' => $feedback_loggedin ?? false,
			'feedback_user_mail' => $feedback_user_mail ?? null,
			'feedback_user_name' => $feedback_user_name ?? null,
			'product' => config('app.name'),
			'version' => config("dms.version"),
			'route' => ! is_null(\Route::getCurrentRoute()->getName()) ? \Route::getCurrentRoute()->getName() : \Route::getCurrentRoute()->getPath(),
			'context' => isset($context) ? e($context) : null,
			'group' => isset($context_group) ? e($context_group) : null,
			'visibility' => isset($current_visibility) ? e($current_visibility) : null,
			'search_terms' => isset($search_terms) ? e($search_terms) : null,
		])

	</body>
	
</html>
		

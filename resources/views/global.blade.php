<!DOCTYPE html>
<html class="no-js" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>
					@stack('title')
        	@if( isset($pagetitle) ) {{ $pagetitle }} &ndash; @endif 
        	{{ config('app.name') }}
        </title>
        <meta name="description" content="{{ $pagedescription ?? config('app.name') }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">

		<link rel="stylesheet" href="{{ css_asset("css/vendor.css") }}">
		{{-- <link rel="stylesheet" href="{{ css_asset("css/app.css") }}"> --}}
		<link rel="stylesheet" href="{{ css_asset("css/app-evolution.css") }}">

		
		<script type="text/javascript" src="{{ js_asset("js/vendor.js") }}"></script>
		
		@include('require-config')
                
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

		@stack('meta')

		@include('analytics.analytics')

	</head>
	<body class="{{$body_classes}}" id="js-drop-area">

		<div class="long-running-message" id="long-running-message">
			{!! trans('notices.long_running_msg') !!}
		</div>


		<div id="app" class="flex flex-col h-screen max-h-screen">
			@section('header')
				@include('headers.header')
			@endsection
	
			@yield('header')

			@if(is_readonly())
				<div class="bg-yellow-400 p-2">
					{!!trans('errors.503-readonly_text_styled')!!}
				</div>
			@endif
	
			<div class="bg-yellow-400 p-2 hidden" id="js-outdated">
				<span class="">
					{{ trans('errors.oldbrowser.generic') }}
					<a href="{{route('browserupdate')}}" class="text-black underline">{{ trans('errors.oldbrowser.more_info') }}</a>.
				</span>
			</div>

			<div class="min-h-0 flex-shrink-0 flex-grow px-2 lg:px-4 " id="page" role="content">
	
				@yield('content')
	
			</div>
	
			@yield('footer')
		</div>

		@yield('panels')

	

	@yield('scripts')

	@stack('js')
	

<!--[if lte IE 10]>
	<script>
		document.body.setAttribute('class', document.body.getAttribute('class') + " ie");

		var message = document.getElementById('js-outdated'),
			isVisible = false,
			div = document.createElement('div');

			if (!('boxShadow' in div.style)){
				isVisible = true;
			}

			if (!('transition' in div.style)){
				isVisible = true;
			} 

			if(isVisible && message){
				message.setAttribute('class', message.getAttribute('class') + " block");
			}
	</script>
<![endif]-->
	
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
		

<!DOCTYPE html>
<html class="no-js" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>
        	@if( isset($pagetitle) ) {{ $pagetitle }} &ndash; @endif 
        	{{ config('app.name') }}
        </title>
        <meta name="description" content="{{ $pagedescription ?? config('app.name') }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">

		<link rel="stylesheet" href="{{ css_asset("css/vendor.css") }}">
		<link rel="stylesheet" href="{{ css_asset("css/app.css") }}">

		
		<script type="text/javascript" src="{{ js_asset("js/vendor.js") }}"></script>
		
		@include('require-config')
                
		<meta name="token" content="{{{ csrf_token() }}}">

		<meta name="base" content="{{ url('/') }}/">
		
		<link rel="apple-touch-icon" sizes="57x57" href="{{ url('/') }}/apple-touch-icon-57x57.png?v=1">
		<link rel="apple-touch-icon" sizes="60x60" href="{{ url('/') }}/apple-touch-icon-60x60.png?v=1">
		<link rel="apple-touch-icon" sizes="72x72" href="{{ url('/') }}/apple-touch-icon-72x72.png?v=1">
		<link rel="apple-touch-icon" sizes="76x76" href="{{ url('/') }}/apple-touch-icon-76x76.png?v=1">
		<link rel="apple-touch-icon" sizes="114x114" href="{{ url('/') }}/apple-touch-icon-114x114.png?v=1">
		<link rel="apple-touch-icon" sizes="120x120" href="{{ url('/') }}/apple-touch-icon-120x120.png?v=1">
		<link rel="apple-touch-icon" sizes="144x144" href="{{ url('/') }}/apple-touch-icon-144x144.png?v=1">
		<link rel="apple-touch-icon" sizes="152x152" href="{{ url('/') }}/apple-touch-icon-152x152.png?v=1">
		<link rel="apple-touch-icon" sizes="180x180" href="{{ url('/') }}/apple-touch-icon-180x180.png?v=1">
		<link rel="icon" type="image/png" href="{{ url('/') }}/favicon-32x32.png?v=1" sizes="32x32">
		<link rel="icon" type="image/png" href="{{ url('/') }}/android-chrome-192x192.png?v=1" sizes="192x192">
		<link rel="icon" type="image/png" href="{{ url('/') }}/favicon-96x96.png?v=1" sizes="96x96">
		<link rel="icon" type="image/png" href="{{ url('/') }}/favicon-16x16.png?v=1" sizes="16x16">
		<link rel="manifest" href="{{ url('/') }}/manifest.json">
		<meta name="apple-mobile-web-app-title" content="K-Box">
		<meta name="application-name" content="K-Box">
		<meta name="msapplication-TileColor" content="#603cba">
		<meta name="msapplication-TileImage" content="{{ url('/') }}/mstile-144x144.png?v=1">
		<meta name="theme-color" content="#ffffff">

		@stack('meta')

		@include('analytics')

	</head>
	<body class="{{$body_classes}}" id="js-drop-area">

		<div class="long-running-message" id="long-running-message">
			{!! trans('notices.long_running_msg') !!}
		</div>


		@section('header')
			@include('headers.header')
		@endsection

		@yield('header')
		

		<!-- Content -->
		<div class="c-page-container" id="page" role="content">

			@yield('content')

		</div>
		<!-- /Content -->

		@include('footer')

		@yield('panels')

	

	@yield('scripts')
	

<!--[if lte IE 9]>
	<script>
		document.body.setAttribute('class', document.body.getAttribute('class') + " ie");

		var message = document.getElementById('js-outdated'),
			isVisible = false,
			dismissed = window.localStorage && window.localStorage.kboxBrowserWarningDismissed ? window.localStorage.kboxBrowserWarningDismissed === 'true' : false;
			div = document.createElement('div');

			if (!('boxShadow' in div.style)){
				isVisible = true;
			}

			if (!('transition' in div.style)){
				isVisible = true;
			} 

			if(isVisible && !dismissed){
				document.body.setAttribute('class', document.body.getAttribute('class') + " outdated--shown");

				document.getElementById('js-outdated-dismiss').addEventListener('click', function(){
					message.setAttribute('class', "c-message c-message--warning outdated js-outdated");
					window.localStorage.kboxBrowserWarningDismissed = 'true';
				});

				message.setAttribute('class', message.getAttribute('class') + " outdated--visible");
				
			}
	</script>
<![endif]-->
	
    @if(support_token() !== false)
	
	<script>
		
		<?php 
        
        $support_context = json_encode([
            'product' => config('app.name'),
            'version' => \Config::get("dms.version"),
            'route' => ! is_null(\Route::getCurrentRoute()->getName()) ? \Route::getCurrentRoute()->getName() : \Route::getCurrentRoute()->getPath(),
            'context' => isset($context) ? $context : null,
            'group' => isset($context_group) ? $context_group : null,
            'visibility' => isset($current_visibility) ? $current_visibility : null,
            'search_terms' => isset($search_terms) ? e($search_terms) : null,
        ]);
        
        ?>
		
		UserVoice=window.UserVoice||[];(function(){var uv=document.createElement('script');uv.type='text/javascript';uv.async=true;uv.src='//widget.uservoice.com/{{ support_token() }}.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(uv,s)})();

		UserVoice.push(['set', {
		  accent_color: '#448dd6',
		  trigger_color: 'white',
          locale: '{{ \App::getLocale() }}',
		  trigger_background_color: '#448dd6',
		  ticket_custom_fields: {
			
			'context': '{!!$support_context!!}'
			
		  },
		}]);
		
		@if(isset($feedback_loggedin) && $feedback_loggedin)
		UserVoice.push(['identify', {
		  email:      '{{$feedback_user_mail}}',
		  name:       '{{$feedback_user_name}}',
		}]);
		
		@endif
		
		UserVoice.push(['addTrigger', { mode: 'contact', trigger_position: 'bottom-right' }]);
		UserVoice.push(['autoprompt', {}]);
	</script>
    
    @endif

	</body>
	
</html>
		

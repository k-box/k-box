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

		<link rel="stylesheet" href="{{ mix("css/vendor.css") }}">
		<link rel="stylesheet" href="{{ mix("css/app-evolution.css") }}">
	
		<script type="text/javascript" src="{{ mix("js/vendor.js") }}"></script>

        <script type="module" src="{{ mix("js/evolution.js") }}"></script>
        <script>
            /**
            * Safari 10.1 `nomodule` support
            * https://gist.github.com/samthor/64b114e4a4f539915a95b91ffd340acc
            * https://philipwalton.com/articles/deploying-es2015-code-in-production-today/
            */
            (function() {
            var d = document;
            var c = d.createElement('script');
            if (!('noModule' in c) && 'onbeforeload' in c) {
                var s = false;
                d.addEventListener('beforeload', function(e) {
                if (e.target === c) {
                    s = true;
                } else if (!e.target.hasAttribute('nomodule') || !s) {
                    return;
                }
                e.preventDefault();
                }, true);

                c.type = 'module';
                c.src = '.';
                d.head.appendChild(c);
                c.remove();
            }
            }());
        </script>
        <script nomodule src="{{ mix("js/evolution-ie11.js") }}"></script>
		
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
	<body class="{{ collect($body_classes ?? [])->merge(['m-0 font-sans antialiased'])->join(' ') }}" id="js-drop-area">
		<div class="relative min-h-screen ">

			{{-- Main application content --}}
			@yield('application')

		</div>

		{{-- Panels and dialogs --}}
		@yield('panels')

		{{-- Additional Javascript --}}
		@yield('scripts')

		@stack('js')

	</body>
	
</html>
		

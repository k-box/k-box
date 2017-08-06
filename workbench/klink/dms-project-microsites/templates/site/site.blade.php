<!DOCTYPE html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>
        	@if( isset($title) ) {{ $title }} &ndash; @endif 
        	{{ config('app.name') }}
        </title>
        <meta name="description" content="@if( isset($description) ) {{ $description }} &ndash; @endif {{ config('app.name') }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">

		<link rel="stylesheet" href="{{ url( "css/microsite.css" ) }}">
                
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
		<meta name="apple-mobile-web-app-title" content="K-Link DMS">
		<meta name="application-name" content="K-Link DMS">
		<meta name="msapplication-TileColor" content="#603cba">
		<meta name="msapplication-TileImage" content="{{ url('/') }}/mstile-144x144.png?v=1">
		<meta name="theme-color" content="#ffffff">

	</head>
	<body class="{{isset($body_classes) ? $body_classes : ''}}">

        @include('sites::site.header')

        @include('sites::site.content')

        @include('sites::site.footer')

	</body>
	
</html>

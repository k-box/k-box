<footer class="container footer" role="footer">
	
	<p>
		
	&copy; 2014-{{date('Y')}} K-Link.
	<span class="version hint--top" data-hint="{{ config('dms.build') }}">{{ config('app.name') }} {{ config('dms.version') }}</span>.

	@if(!isset($not_show_links))

		<span class="links">
			
			<a href="{{ route('help') }}">{{trans('pages.help')}}</a>
			
			<a href="{{ route('privacy.summary') }}">{{trans('pages.privacy')}}</a>

			<a href="{{ route('terms') }}">{{trans('pages.terms')}}</a>

			<a href="{{ route('contact') }}">{{trans('pages.contact')}}</a>

		</span>

	@endif

	</p>

</footer>
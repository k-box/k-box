<footer class="container footer" role="footer">
	
	<p>
		
	&copy; 2014-{{date('Y')}} K-Link.
	<span class="version hint--top" data-hint="{{\App::environment()}} {{ Config::get('dms.build') }}">DMS v{{ Config::get('dms.version') }}</span>.

	@if(!isset($not_show_links))

		<span class="links">
			
			<a href="{{ route('help') }}">{{trans('pages.help')}}</a>

			<a href="{{ route('contact') }}">{{trans('pages.contact')}}</a>

		</span>

	@endif


	</p>


	<!-- Made in Italy (in front of a carwash) -->
</footer>
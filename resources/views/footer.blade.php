<footer class="container footer p-4 mt-8 mx-auto" role="footer">
	
	@if(!isset($not_show_links))

		<ul class="flex justify-center">
			<li><a href="{{ route('help') }}">{{trans('pages.help')}}</a><span class="px-2" aria-hidden="true">&middot;</span></li>
			<li><a href="{{ route('privacy.summary') }}">{{trans('pages.privacy')}}</a><span class="px-2" aria-hidden="true">&middot;</span></li>
			<li><a href="{{ route('terms') }}">{{trans('pages.terms_long')}}</a><span class="px-2" aria-hidden="true">&middot;</span></li>
			<li><a href="{{ route('contact') }}">{{trans('pages.contact')}}</a></li>
		</ul>

	@endif

</footer>
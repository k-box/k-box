<footer class="container footer p-4 mt-8 mx-auto" role="footer">
	
	@if(!isset($not_show_links))

		<ul class="flex justify-center">
			<li><a class="text-gray-700 hover:text-gray-800 active:text-gray-900 focus:text-gray-900" href="{{ route('help') }}">{{trans('pages.help')}}</a><span class="px-2" aria-hidden="true">&middot;</span></li>
			<li><a class="text-gray-700 hover:text-gray-800 active:text-gray-900 focus:text-gray-900" href="{{ route('privacy.summary') }}">{{trans('pages.privacy')}}</a><span class="px-2" aria-hidden="true">&middot;</span></li>
			<li><a class="text-gray-700 hover:text-gray-800 active:text-gray-900 focus:text-gray-900" href="{{ route('terms') }}">{{trans('pages.terms_long')}}</a><span class="px-2" aria-hidden="true">&middot;</span></li>
			<li><a class="text-gray-700 hover:text-gray-800 active:text-gray-900 focus:text-gray-900" href="{{ route('contact') }}">{{trans('pages.contact')}}</a></li>
		</ul>

	@endif

</footer>
<footer class="container footer p-4 mt-8 mx-auto" role="footer">
	
	@if(!isset($not_show_links))

		<ul class="flex justify-center">
			<li><a class="text-gray-700 hover:text-gray-800 active:text-gray-900 focus:text-gray-900" href="{{ route('help') }}">{{trans('pages.help')}}</a></li>
			@haspage(\KBox\Pages\Page::PRIVACY_POLICY_LEGAL)
				<li><span class="px-2" aria-hidden="true">&middot;</span><a class="text-gray-700 hover:text-gray-800 active:text-gray-900 focus:text-gray-900" href="{{ route('privacy.legal') }}">{{trans('pages.privacy')}}</a></li>
			@endhaspage
			@haspage(\KBox\Pages\Page::TERMS_OF_SERVICE)
				<li><span class="px-2" aria-hidden="true">&middot;</span><a class="text-gray-700 hover:text-gray-800 active:text-gray-900 focus:text-gray-900" href="{{ route('terms') }}">{{trans('pages.terms_long')}}</a></li>
			@endhaspage
			@if(\KBox\Option::areContactsConfigured())
				<li><span class="px-2" aria-hidden="true">&middot;</span><a class="text-gray-700 hover:text-gray-800 active:text-gray-900 focus:text-gray-900" href="{{ route('contact') }}">{{trans('pages.contact')}}</a></li>
			@endif
		</ul>

	@endif

</footer>
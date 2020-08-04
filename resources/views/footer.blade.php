<footer class="px-4 mt-10 sm:mt-12 pb-10 sm:mb-12 sm:px-6 md:mt-16 md:mb-0 lg:mt-20 lg:px-8 xl:mt-28 flex flex-col items-start sm:items-center lg:items-start" role="footer">
	
	@if(!isset($not_show_links))

		<ul class="flex">
			<li><a class="text-gray-700 hover:text-blue-700 focus:text-blue-700 active:text-blue-900" href="{{ route('help') }}">{{trans('pages.help')}}</a></li>
			@haspage(\KBox\Pages\Page::PRIVACY_POLICY_LEGAL)
				<li><span class="px-2" aria-hidden="true">&middot;</span><a class="text-gray-700 hover:text-blue-700 focus:text-blue-700 active:text-blue-900 " href="{{ route('privacy.legal') }}">{{trans('pages.privacy')}}</a></li>
			@endhaspage
			@haspage(\KBox\Pages\Page::TERMS_OF_SERVICE)
				<li><span class="px-2" aria-hidden="true">&middot;</span><a class="text-gray-700 hover:text-blue-700 focus:text-blue-700 active:text-blue-900 " href="{{ route('terms') }}">{{trans('pages.terms_long')}}</a></li>
			@endhaspage
			@if(\KBox\Option::areContactsConfigured())
				<li><span class="px-2" aria-hidden="true">&middot;</span><a class="text-gray-700 hover:text-blue-700 focus:text-blue-700 active:text-blue-900 " href="{{ route('contact') }}">{{trans('pages.contact')}}</a></li>
			@endif
		</ul>

	@endif

	<p class="text-gray-600 text-sm">
		{{ __('Powered by the') }} <a href="https://github.com/k-box/k-box/" rel="noopener noreferrer" class="text-gray-600 hover:text-blue-700 focus:text-blue-700" target="_blank">{{ __('K-Box Open Source project') }}</a>
	</p>

</footer>
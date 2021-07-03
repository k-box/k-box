


<div class="c-panel z-30 js-panel">
	
	<div class="c-panel__cache close js-panel-close">
	</div>


	<div class="c-panel__content">
		<a href="#close" title="{{trans('panels.close_btn')}}" class="button c-panel__close p-2 js-panel-close">@materialicon('navigation', 'close')</a>

		<div class="js-panel-content" style="height:100%">
			{{trans('panels.loading_message')}}

			@yield('panelcontent')
		</div>

	</div>
</div>


<div x-cloak x-data="Dialog()" 
	 x-show="open" 
	 x-init="init" 
	 @mousedown.away="hide" 
	 @dialog-show.window="showDialog" 
	 @dialog-close.window="hide" 
	 @keydown.window.escape="hide" 
	 :class="{'fixed' : open, 'pointer-events-none' : !open }" 
	 class="pointer-events-none bottom-0 inset-x-0 px-4 pb-4 sm:inset-0 sm:flex sm:items-center sm:justify-center z-50 max-h-screen">
  <div x-cloak :class="{'fixed' : open, 'opacity-0': !open }" @click="hide" class="inset-0 transition-opacity">
    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
  </div>
  <div x-cloak class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full" 
		  role="dialog" aria-modal="true" aria-labelledby="modal-headline">
		 <template x-if="loading">
			<div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
				<div class="sm:flex sm:items-start">
					<div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 sm:mx-0 sm:h-10 sm:w-10">
						
					</div>
					<div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
						<h3 class="h-5 leading-6 font-medium bg-gray-200 w-2/3">
						</h3>
						<div class="mt-2 h-4 bg-gray-200 w-2/4"></div>
						<div class="mt-2 h-4 bg-gray-200 w-3/4"></div>
					</div>
				</div>
				<div class="px-4 py-3 h-8 sm:px-6"></div>
			</div>
		 </template>
		 <template x-if="!loading && message">
			<div x-html="message"></div>
		 </template>
		 <template x-if="!loading && errorMessage">
			<div>
				<div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
					<div class="sm:flex sm:items-start">
						<div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
							@materialicon('alert', 'error_outline', 'h-6 w-6 text-red-600 fill-current')
						</div>
						<div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
							<h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
								{{ trans('panels.load_error_alt') }}
							</h3>
							<div class="mt-2">
								<p x-html="errorMessage" class="text-sm leading-5 text-gray-900">
								</p>
							</div>
						</div>
					</div>
				</div>
				<div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
					<span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
						<button type="submit" @click="$dispatch('dialog-close', {})" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-blue-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-500 transition ease-in-out duration-150 sm:text-sm sm:leading-5">
							{{ trans('panels.close_btn') }}
						</button>
					</span>
				</div>
			</div>
		 </template>
  </div>
</div>

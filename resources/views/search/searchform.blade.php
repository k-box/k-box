
<form role="search" method="get" class="search-form flex flex-grow max-w-lg mr-4" id="main-search" data-bind="search" action="{{ isset($search_target_parameters) && !!$search_target_parameters ? route($search_target, $search_target_parameters) : route( isset( $search_target ) ? $search_target : 'search' ) }}">

	@if(isset($current_visibility) && ($current_visibility==='private' || $current_visibility==='personal' || $current_visibility==='public'))
		<input type="hidden" name="visibility" value="{{$current_visibility}}">
	@endif
	

	<div class="relative flex-grow">

		<button type="submit" class="absolute right-0 px-2 py-1 text-gray-700" title="{{trans('search.form.submit')}}">
			@materialicon('action', 'search', 'fill-current')
		</button>

		<input class="search-input w-full transition focus:outline-0 border border-transparent focus:bg-white focus:border-gray-700 border-gray-300 text-gray-900 focus:text-black rounded bg-white py-1 pl-2 pr-10 appearance-none leading-normal " type="text" placeholder="{{ isset($filter) ? trans('search.form.placeholder_in', ['location' => $filter]) : trans('search.form.placeholder') }}" autocomplete="off" spellcheck="false" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-label="search input" value="@if(isset($search_terms) && $search_terms!=='*'){{ $search_terms }}@endif" name="s" @if(empty($search_terms)) autofocus @endif>

		<div class="hint px-2 py-1 mt-1 text-white bg-gray-700 rounded w-full">
	{!! isset($filter) ? trans('search.form.hint_in', ['location' => $filter]) : trans('search.form.hint') !!}
		</div>

	</div>
	
    
</form>

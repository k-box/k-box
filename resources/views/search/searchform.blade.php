
@if(isset($search_target_parameters) && !!$search_target_parameters)
<form role="search" method="get" class="search-form" id="main-search" data-bind="search" action="{{ route($search_target, $search_target_parameters) }}">
@else
<form role="search" method="get" class="search-form" id="main-search" data-bind="search" action="{{ route( isset( $search_target ) ? $search_target : 'search' ) }}">
@endif

	@if(isset($current_visibility) && ($current_visibility==='private' || $current_visibility==='personal' || $current_visibility==='public'))
	<input type="hidden" name="visibility" value="{{$current_visibility}}">
	@endif
	

	<div class="hint--bottom hint--info search--hint" data-hint="{!! isset($filter) ? trans('search.form.hint_in', ['location' => $filter]) : trans('search.form.hint') !!}">

    	<input type="search" class="search-field" placeholder="{{ isset($filter) ? trans('search.form.placeholder_in', ['location' => $filter]) : trans('search.form.placeholder') }}" value="@if(isset($search_terms) && $search_terms!=='*'){{ $search_terms }}@endif" name="s" @if(empty($search_terms)) autofocus @endif />

		<button type="submit" class="search-submit" title="{{trans('search.form.submit')}}">
			@materialicon('action', 'search')
		</button>

    </div>
    
</form>

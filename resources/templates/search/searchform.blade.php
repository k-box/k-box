
@if(isset($search_target_parameters) && !!$search_target_parameters)
<form role="search" method="get" class="search-form" id="main-search" data-bind="search" action="{{ route($search_target, $search_target_parameters) }}">
@else
<form role="search" method="get" class="search-form" id="main-search" data-bind="search" action="{{ route( isset( $search_target ) ? $search_target : 'search' ) }}">
@endif

	@if(isset($current_visibility) && ($current_visibility==='private' || $current_visibility==='personal' || $current_visibility==='public'))
	<input type="hidden" name="visibility" value="{{$current_visibility}}">
	@endif
	

	<div class="input-group">

    	<input type="search" class="search-field" placeholder="{{trans('search.form.placeholder')}}" value="@if(isset($search_terms) && $search_terms!=='*'){{ $search_terms }}@endif" name="s" @if(empty($search_terms)) autofocus @endif />
    

		@if(!isset($context))

    	<div class="input-group-addon visibility-switcher" id="visibility-switcher">
    		<a href="#public" data-bind="public" title="{{trans('search.form.public_switch_alt')}}" class="item @if(isset($current_visibility) && $current_visibility=='public') current @endif">
	    		<span class="icon icon-social-black icon-social-black-ic_public_black_24dp"></span>
	    		<span class="label">{{trans('documents.visibility.public')}}</span>
	    	</a>
	    	@if($is_user_logged)
		    	<a href="#private" data-bind="private" title="{{trans('search.form.private_switch_alt')}}" class="item @if(isset($current_visibility) && $current_visibility=='private') current @endif">
		    		<span class="icon icon-action-black icon-action-black-ic_lock_black_24dp"></span>
		    		<span class="label">{{trans('documents.visibility.private')}}</span>
		    	</a>
		    @endif
    	</div>
		
		@elseif(isset($context) && isset($filter))
		
			<div class="input-group-addon visibility-switcher" id="visibility-switcher">
		
				<a href="#public" title="{{$context}} - {{$filter}}" class="item current">
		    		<span class="icon ">in <strong>{{$filter}}</strong></span>
		    		<span class="label"></span>
		    	</a>
			</div>
		
		@endif



    	

    </div>
    <button type="submit" class="search-submit" title="{{trans('search.form.submit')}}">
    	<span class="icon-action-black icon-action-black-ic_search_black_24dp"></span>
    </button>
</form>

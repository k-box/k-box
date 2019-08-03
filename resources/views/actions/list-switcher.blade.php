<div class="switcher hidden md:flex flex-no-wrap" id="list-switcher" data-list="#documents-list .list" data-current="{{$list_style_current}}">

	<button data-list="details" class="action__button  ml-0" title="{{trans('actions.switcher.details')}}">
		@materialicon('action', 'view_list', 'inline-block')
	</button> 
	<button data-list="tiles" class="action__button ml-0" title="{{trans('actions.switcher.tiles')}}">
		@materialicon('action', 'view_stream', 'inline-block')
	</button>
	<button data-list="cards" class="action__button  ml-0" title="{{trans('actions.switcher.grid')}}">
		@materialicon('action', 'view_module', 'inline-block')
	</button>

</div>

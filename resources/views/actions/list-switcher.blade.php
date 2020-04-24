<div class="switcher flex flex-no-wrap" id="list-switcher" data-list="#documents-list .list" data-current="{{$list_style_current}}">

	<button data-list="details" class="button p-2 ml-0 rounded-r-none" title="{{trans('actions.switcher.details')}}">
		@materialicon('action', 'view_list', 'inline-block')
	</button> 
	<button data-list="tiles" class="button p-2 ml-0 rounded-none" title="{{trans('actions.switcher.tiles')}}">
		@materialicon('action', 'view_stream', 'inline-block')
	</button>
	<button data-list="cards" class="button p-2 ml-0 rounded-l-none" title="{{trans('actions.switcher.grid')}}">
		@materialicon('action', 'view_module', 'inline-block')
	</button>

</div>

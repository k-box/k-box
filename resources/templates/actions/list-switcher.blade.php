
<div class="action-group switcher" id="list-switcher" data-list="#documents-list .list" data-current="{{$list_style_current}}"> <!--.list --> 

<a href="#" data-list="details" class="switch hint--bottom" data-hint="{{trans('actions.switcher.details')}}">
	<span class="icon-action-white icon-action-white-ic_view_list_white_24dp"></span>
</a> 
<a href="#" data-list="tiles" class="switch hint--bottom" data-hint="{{trans('actions.switcher.tiles')}}">
	<span class="icon-action-white icon-action-white-ic_view_stream_white_24dp"></span>
</a>
<a href="#" data-list="cards" class="switch hint--bottom" data-hint="{{trans('actions.switcher.grid')}}">
	<span class="icon-action-white icon-action-white-ic_view_module_white_24dp"></span>
</a>

@if(isset($list_style_map_enabled) && $list_style_map_enabled)
<a href="#" data-list="map" class="switch" title="{{trans('actions.switcher.map')}}">
	<span class="icon-maps-white icon-maps-white-ic_map_white_24dp"></span>
</a>
@endif

</div>

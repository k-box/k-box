
<div class="action-group item-selector" id="item-selector">

	<div class="selection-button "  data-action="selection-button" >
		
		<span class="btn-icon tristate hint--buttom" data-hint="{{trans('actions.selection.hint')}}"><input type="checkbox" name="selection-tristate" id="selection-tristate" class="tristate-checkbox" /> </span>

		<ul class="drop-menu" style="display:none">
			<li><a href="#" class="dropdown-el"  data-action="clear">{{trans('actions.selection.clear')}}</a></li>
			<li><a href="#" class="dropdown-el"  data-action="all">{{trans('actions.selection.all')}}</a></li>
			<li><a href="#" class="dropdown-el"  data-action="invert">{{trans('actions.selection.invert')}}</a></li>
		</ul>

	</div>

</div>

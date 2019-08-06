


<div class="action__button tristate-button js-document-selection-button tristate-button--none hint--bottom"  data-hint="{{trans('actions.selection.hint')}}">
{{-- data-action="selection-button" --}}
	
	<input type="checkbox" name="selection-tristate" id="selection-tristate" style="display:none" />


	@materialicon('toggle', 'check_box', 'inline-block tristate__all')
	@materialicon('toggle', 'indeterminate_check_box', 'inline-block tristate__partial')
	@materialicon('toggle', 'check_box_outline_blank', 'inline-block tristate__none')


</div>

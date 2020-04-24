<div class="button js-document-selection-button hint--bottom mr-2 p-2"  data-hint="{{trans('actions.selection.hint')}}">	
	<input type="checkbox" name="selection-tristate" id="selection-tristate" class="hidden" />

	<div class="hidden js-check-all">
		@materialicon('toggle', 'check_box', '')
	</div>
	<div class="hidden js-check-partial">
		@materialicon('toggle', 'indeterminate_check_box', '')
	</div>
	<div class="block js-check-none">
		@materialicon('toggle', 'check_box_outline_blank', '')
	</div>
</div>

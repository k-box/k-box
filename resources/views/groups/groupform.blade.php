<div class="mt-2 w-full">
	<input type="text" 
		x-model="text" 
		class="form-input block w-full"
		name="name" 
		required 
		value="{{ optional($group ?? null)->name }}" />
</div>

<div class="mt-2">
	<p class="text-sm leading-5 text-gray-900">
		
		@if(isset($show_parent) && $show_parent)
		
			@if(isset($private) && $private)
				{!! trans('groups.form.parent_label', ['parent' => e($parent_label)]) !!}
			@else
				{!! trans('groups.form.parent_project_label', ['parent' => e($parent_label)]) !!}
			@endif
			<input type="hidden" name="parent" value="{{$parent_id}}">
		
		@endif
	</p>
</div>

@if(isset($is_public_collection) && $is_public_collection)

	<input type="hidden" name="public" value="1" id="grp_to_public">

@endif
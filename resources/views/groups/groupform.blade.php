<input type="text" 
	x-model="text" 
	class="form-input block  w-full"
	name="name" 
	required 
	value="@if(isset($group)){{$group->name}}@endif" />

<p x-text="text"></p>

@if(isset($show_parent) && $show_parent)

	@if(isset($private) && $private)
		<label>{!! trans('groups.form.parent_label', ['parent' => e($parent_label)]) !!}</label>
	@else
		<label>{!! trans('groups.form.parent_project_label', ['parent' => e($parent_label)]) !!}</label>
	@endif
	<input type="hidden" name="parent" value="{{$parent_id}}">

@endif

@if(isset($is_public_collection) && $is_public_collection)

	<input type="hidden" name="public" value="1" id="grp_to_public">

@endif
@if(($user_can_edit_public_groups || $user_can_edit_private_groups))

<form method="POST" class="c-form" action="@if(!isset($group) || (isset($create) && $create)) {{route('documents.groups.store')}} @else {{route('documents.groups.update', $group->id)}} @endif" rv-on-submit="groupSubmit">

	{{ csrf_field() }}

	@if(isset($edit) && $edit)

		<input type="hidden" name="_method" value="PUT">

	@endif

	<div class="c-form__field">
	<label>{{trans('groups.form.collection_name_label')}}</label>
	<input type="text" class="c-form__input" name="name" required value="@if(isset($group)){{$group->name}}@endif" placeholder="{{trans('groups.form.collection_name_placeholder')}}" />

	@if(isset($show_parent) && $show_parent)

		@if(isset($private) && $private)
			<label>{{trans('groups.form.parent_label')}}</label>
		@else
			<label>{{trans('groups.form.parent_project_label')}}</label>
		@endif
		<input type="hidden" name="parent" value="{{$parent_id}}">
		<p>{{$parent_label}}</p>

	@endif

	@if(isset($is_public_collection) && $is_public_collection)
	
		<input type="hidden" name="public" value="1" id="grp_to_public">
	
	@endif
	
	</div>


	<div class="c-form__buttons">
		@if(isset($main_action))
		<button type="submit" class="button button--primary">
			<span class="button__normal">{{$main_action}}</span>
			<span class="button__processing">{{trans('groups.loading')}}</span>
		</button>
		@endif
	
		@if(isset($show_cancel) && $show_cancel)
	
		{!!trans('actions.or', ['action' => '<a href="#"  class="cancel js-cancel">'.trans('actions.cancel').'</a>'])!!} 
	
		@endif
	</div>

</form>
@endif
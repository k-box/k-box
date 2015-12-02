@if(($user_can_edit_public_groups || $user_can_edit_private_groups))

<form method="POST" action="@if(!isset($group) || (isset($create) && $create)) {{route('documents.groups.store')}} @else {{route('documents.groups.update', $group->id)}} @endif" rv-on-submit="groupSubmit">

	<input type="hidden" name="_token" value="{{{ csrf_token() }}}">

	@if(isset($edit) && $edit)

		<input type="hidden" name="_method" value="PUT">

	@endif

	<label>{{trans('groups.form.collection_name_label')}}</label>
	<input type="text" name="name" required value="@if(isset($group)){{$group->name}}@endif" placeholder="{{trans('groups.form.collection_name_placeholder')}}" />

	@if(isset($show_parent) && $show_parent)

		<label>{{trans('groups.form.parent_label')}}</label>
		<input type="hidden" name="parent" value="{{$parent_id}}">
		<p>{{$parent_label}}</p>

	@endif

	@if(isset($is_public_collection) && $is_public_collection)
	
		<input type="hidden" name="public" value="1" id="grp_to_public">
	
	@endif
	


	<p>
		@if(isset($main_action))
		<button type="submit" class="two-state-button">
			<span class="base-state">{{$main_action}}</span>
			<span class="loading-state">{{trans('groups.loading')}}</span>
		</button>
		@endif
	
		@if(isset($show_cancel) && $show_cancel)
	
		{!!trans('actions.or', ['action' => '<a href="#"  class="cancel">'.trans('actions.cancel').'</a>'])!!} 
	
		@endif
	</p>

</form>
@endif
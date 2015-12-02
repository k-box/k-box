@if(!$group->is_private || ($group->is_private && $group->user_id === $current_user))
<li class="tree-item groups-menu" data-group-id="{{$group->id}}" data-isprivate="{{$group->is_private ? 'true':'false'}}">

	<a href="{{ DmsRouting::group($group->id) }}" title="{{$group->name}}" class="tree-item-inner @if(\Request::is('*groups/'. $group->id)) current @endif" data-group-id="{{$group->id}}" data-drop="true" 
		@if($group->is_private || !$group->isRoot()) draggable="true" @endif 
		data-drag-el="group" data-drop-action="copyTo" 
		@if( isset($badge_shared) && $badge_shared ) data-shared="true"  @endif 
		data-isprivate="{{$group->is_private ? 'true':'false'}}">

		<?php $has_children = isset($group->children) && !$group->children->isEmpty() ; ?>
		
		<span class="group-color" @if($group->color) style="background-color:#{{$group->color}} " @endif></span>
		
		<span rv-on-click="groups.expandOrCollapse" data-expanded="false" class="tree-chevron @if(!$has_children) hidden @endif collapsed"></span>
		
		{{$group->name}}
		
	</a>


	@if($has_children)

		<ul class="tree-childs collapsed groups-sub">

			@foreach($group->children as $child)

				@include('groups.tree-item', ['group' => $child])	

			@endforeach

		</ul>

	@endif

</li>
@endif
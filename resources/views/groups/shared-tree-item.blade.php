<div class="js-tree-item navigation__spacer js-groups-menu" data-group-id="{{$group->id}}" 
	data-isprivate="{{$group->is_private ? 'true':'false'}}"
	@if(!is_null($group->project) && $group->project)  data-class="project" data-id="{{$group->project->id}}" data-project="{{$group->project->id}}" @else  data-class="group" data-id="{{$group->id}}"  @endif >

	<a href="{{ DmsRouting::group($group->id) }}" title="{{$group->name}}" data-group-name="{{$group->name}}" class="navigation__item navigation__item--link js-tree-item-inner @if(\Request::is('*groups/'. $group->id)) navigation__item--current js-tree-current @endif" data-group-id="{{$group->id}}" data-drop="true" 
		@if($group->is_private || !$group->isRoot()) draggable="true" @endif 
		data-drag-el="group" data-drop-action="copyTo" 
		@if(!is_null($group->project) && $group->project) data-project="{{$group->project->id}}" @endif 
		@if( isset($badge_shared) && $badge_shared ) data-shared="true"  @endif 
		data-isprivate="{{$group->is_private ? 'true':'false'}}">

		<?php $has_children = $group->hasChildrenRelation(); ?>
		
		<span class="navigation__color-badge" @if($group->color) style="background-color:#{{$group->color}} " @endif></span>
		
		<button rv-on-click="groups.expandOrCollapse" data-expanded="false" class="js-tree-chevron navigation__expander @if(!$has_children) navigation__expander--hidden @endif">
			@materialicon('hardware', 'keyboard_arrow_right', 'navigation__expand')
			@materialicon('hardware', 'keyboard_arrow_down', 'navigation__collapse')
		</button>
		
		{{$group->name}}
		
	</a>


	@if($has_children)

		<div class="navigation__expandable navigation__expandable--collapsed">

			@foreach($group->getChildren() as $child)

				@include('groups.shared-tree-item', ['group' => $child])	

			@endforeach

		</div>

	@endif

</div>
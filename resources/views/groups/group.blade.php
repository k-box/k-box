
@component('components.list-item', 
	[
		'id' => $item->id,
		'type' => 'group',
		'data_class' => "group",
		'draggable' => true,
		'selectable' => true,
		'drop_action' => 'copyTo',
		'trashed' => $item->trashed(),
		'share' => isset($share_id) ? $share_id : false,
		'shared_with' => isset($shared_with) ? true : false,
		'thumbnail' => !is_null($item->project) ? $item->project->avatar : false,
		'color' => $item->color,
		'name' => $item->name,
		'url' => route( isset($link_route) ? $link_route : 'documents.groups.show', $item->id),
		'added_by' => $item->user->name,
		'modified_at' => $item->updated_at,
		'created_at' => $item->created_at,
		'modified_at_diff' => $item->getUpdatedAtHumanDiff(),
		'language' => '',
		'shared_on' => $share_created_at ?? null,
		'shared' => isset($badge_shared) && $badge_shared,
		'shared_by' => isset($shared_by) ? $shared_by : false,
		'starrable' => false,
		'project' => !is_null($item->project) ? $item->project->id : null,
	])

@endcomponent

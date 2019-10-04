
@if(class_basename(get_class($item)) === 'Group')

@include('groups.group')

@elseif (class_basename(get_class($item)) === 'DocumentDescriptor')


	@component('components.list-item', 
		[
			'id' => $item->id,
			'instance' => $item,
			'local_document_id' => $item->local_document_id,
			'type' => $item->document_type,
			'data_class' => "document",
			'draggable' => true,
			'selectable' => true,
			'trashed' => $item->trashed(),
			'visibility' => $item->visibility,
			'is_public' => $item->is_public,
			'is_published' => $item->isPublished(),
			'star' => isset($star_id) ? $star_id : false,
			'share' => isset($share_id) ? $share_id : false,
			'shared_with' => isset($shared_with) ? true : false,
			'thumbnail' => DmsRouting::thumbnail($item),
			'name' => $item->title,
			'url' => route('documents.show', $item->id),
			'added_by' => (!is_null($item->owner) ? $item->owner->name .' ' : ''),
			'modified_at' => $item->updated_at,
			'modified_at_diff' => $item->getUpdatedAtHumanDiff(),
			'language' => isset($item->language) && in_array($item->language, config('dms.language_whitelist')) ? $item->language : '',
			'shared_by' => isset($shared_by) ? $shared_by : false,
			'shared_on' => $share_created_at ?? null,
			'shared' => $item->isShared(),
			'starrable' => isset($is_starrable) && (!isset($context) || isset($context) && $context !== 'trash') ? $is_starrable : false,
			'starred' => isset($is_starred) ? $is_starred : false,
			'has_duplicates' => isset($badge_duplicate) && $badge_duplicate,
		])

	@endcomponent


@elseif($item)

	@component('components.list-item', 
		[
			'uuid' => $item->uuid,
			'type' => \KBox\Documents\DocumentType::from($item->properties->mime_type),
			'data_class' => "document",
			'draggable' => false,
			'selectable' => false,
			
			'visibility' => 'public',
			'is_public' => true,
			'star' => isset($item->starId) ? $item->starId : false,
			'share' => false,
			'shared_with' => false,
			'thumbnail' => $item->properties->thumbnail,
			'name' => $item->properties->title,
			'url' => $item->url,
			'added_by' => $item->uploader->name,
			'modified_at' => $item->properties->updated_at instanceof \DateTime ? \Carbon\Carbon::instance($item->properties->updated_at)->render() : $item->properties->updated_at,
			'created_at' => $item->properties->created_at instanceof \DateTime ? \Carbon\Carbon::instance($item->properties->created_at)->render() : $item->properties->created_at,
			'language' => $item->properties->language,
			'shared_by' => false,
			'shared' => false,
			'starrable' => $item->isStarrable,
			'starred' => !empty($item->starId),
		])

	@endcomponent


@endif
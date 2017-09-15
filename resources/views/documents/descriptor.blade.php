
@if(class_basename(get_class($item)) === 'Group')

@include('groups.group')

@elseif (class_basename(get_class($item)) === 'DocumentDescriptor')


	@component('components.list-item', 
		[
			'id' => $item->id,
			'local_document_id' => $item->local_document_id,
			'type' => $item->document_type,
			'data_class' => "document",
			'draggable' => true,
			'selectable' => true,
			'trashed' => $item->trashed(),
			'institution' => $item->institution_id,
			'institution_klink_id' => $item->institution->klink_id,
			'visibility' => $item->visibility,
			'is_public' => $item->isPublic(),
			'star' => isset($star_id) ? $star_id : false,
			'share' => isset($share_id) ? $share_id : false,
			'shared_with' => isset($shared_with) ? true : false,
			'thumbnail' => DmsRouting::thumbnail($item),
			'name' => $item->title,
			'url' => route('documents.show', $item->id),
			'added_by' => (!is_null($item->owner) ? $item->owner->name .' ' : ''),
			'modified_at' => $item->getUpdatedAt(true),
			'created_at' => $item->getCreatedAt(true),
			'modified_at_diff' => $item->getUpdatedAtHumanDiff(),
			'created_at_diff' => $item->getCreatedAt(),
			'language' => isset($item->language) ? $item->language : '',
			'shared_by' => isset($shared_by) ? $shared_by : false,
			'shared_on' => isset($share_created_at_timestamp) ? $share_created_at_timestamp : false,
			'shared_on_diff' => isset($share_created_at) ? $share_created_at : false,
			'shared' => $item->isShared(),
			'starrable' => isset($is_starrable) ? $is_starrable : false,
			'starred' => isset($is_starred) ? $is_starred : false,
		])

	@endcomponent


@elseif($item)

	@component('components.list-item', 
		[
			
			'local_document_id' => $item->getLocalDocumentId(),
			'type' => $item->documentType,
			'data_class' => "document",
			'draggable' => false,
			'selectable' => false,
			'institution' => $item->getInstitutionId(),
			
			'visibility' => $item->getVisibility(),
			'is_public' => true,
			'star' => isset($item->starId) ? $item->starId : false,
			'share' => false,
			'shared_with' => false,
			'thumbnail' => $item->thumbnailURI,
			'name' => $item->title,
			'url' => $item->documentURI,
			'added_by' => $item->institutionName,
			'modified_at' => $item->creationDate,
			'created_at' => $item->creationDate,
			'language' => $item->language,
			'shared_by' => false,
			'shared' => false,
			'starrable' => $item->isStarrable,
			'starred' => $item->isStarred,
		])

	@endcomponent


@endif
        
			<div class="file-properties">
	@if(isset($stars_count) && !$document->trashed())

		<div class="stars">

			@materialicon('toggle', 'star'){{trans_choice('starred.starred_count_alt', $stars_count, ['number' => $stars_count])}}

		</div>

	@endif


	@if($document->is_public)

		<div class="is_public" title="{{trans('documents.descriptor.is_public_description')}}">

			@materialicon('social', 'public'){{trans('documents.descriptor.is_public')}}

		</div>

	@endif

	<div class="js-license-details">
		@include('documents.partials.license', ['license' => $document->copyright_usage, 'owner' => $document->copyright_owner])
	</div>
		

	<div class="file-properties__property">
		<span class="file-properties__label">{{trans('panels.groups_section_title')}}</span>
		@include('documents.partials.collections', [
			'document_is_trashed' => $document->trashed(),
			'user_can_edit_public_groups' => false,
			'user_can_edit_private_groups' => false,
			'document_id' =>  $document->id,
			'collections' => $groups,
			'use_groups_page' => $use_groups_page,
			'is_in_collection' => isset($is_in_collection) && $is_in_collection 
		])
	</div>
	
	<div class="file-properties__property">
		<span class="file-properties__label">{{trans('panels.meta.authors')}}</span>
		<span class="file-properties__value">{{ isset($document) ? $document->authors : '' }}</span>
	</div>
	
	<div class="file-properties__property">
		<span class="file-properties__label">{{trans('panels.meta.institution')}}</span>
		<span class="file-properties__value">{{ isset($document) && !is_null($document->institution) ? $document->institution->name : '' }}</span>
	</div>
	
	<div class="file-properties__property">
		<span class="file-properties__label">{{trans('panels.meta.main_contact')}}</span>
		<span class="file-properties__value">{{ isset($document) ? $document->user_owner : '' }}</span>
	</div>
	
	<div class="file-properties__property">
		<span class="file-properties__label">{{trans('panels.meta.language')}}</span>
		<span class="file-properties__value">{{ isset($document) && !empty($document->language) ? trans('languages.' . $document->language) : trans('languages.no_language') }}</span>
	</div>
	
	<div class="file-properties__property">
		<span class="file-properties__label">{{trans('panels.meta.added_on')}}</span>
		<span class="file-properties__value">{{ isset($document) ? $document->getCreatedAt(true) : '' }}</span>
	</div>

	@if($document->trashed())
		<div class="file-properties__property">
			<span class="file-properties__label">{{trans('panels.meta.deleted_on')}}</span>
			<span class="file-properties__value">{{ isset($document) ? $document->getDeletedAt(true) : '' }}</span>
		</div>
	@endif

	@if($document->isFileUploadComplete())
		<div class="file-properties__property">
			<span class="file-properties__label">{{trans('panels.meta.size')}}</span>
			<span class="file-properties__value">{{ isset($document) ? KBox\Documents\Services\DocumentsService::human_filesize($document->file->size) : '' }}</span>
		</div>
	@endif

	<div class="file-properties__property">
		<span class="file-properties__label">{{trans('panels.meta.uploaded_by')}}</span>
		<span class="file-properties__value">{{ isset($document) ? $document->user_uploader : '' }}</span>
	</div>

</div>
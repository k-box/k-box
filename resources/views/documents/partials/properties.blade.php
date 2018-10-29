
<div class="meta info">

	<h4 class="c-panel__section">{{trans('panels.info_section_title')}}</h4>

	@auth	
		<div class="c-panel__meta">
			<div class="c-panel__label">{{trans('panels.meta.uploaded_by')}}</div>
			
			{{ optional($document->owner)->name ?? '-' }}
		</div>
	@endauth

	<div class="c-panel__meta">
		<div class="c-panel__label">{{trans('panels.meta.added_on')}}</div>
		
		{{$document->getCreatedAt(true)}}
	</div>

	<div class="c-panel__meta">
		<div class="c-panel__label">{{trans('documents.descriptor.last_modified')}}</div>
			
		{{$document->getUpdatedAt(true)}}
	</div>

	@if($document->trashed())

		<div class="c-panel__meta">
			<div class="c-panel__label">{{trans('panels.meta.deleted_on')}}</div>

			{{$document->getDeletedAt(true)}}
		</div>

	@endif

	<div class="c-panel__meta">
		<div class="c-panel__label">{{trans('panels.meta.language')}}</div>
		
		{{!empty($document->language) && in_array($document->language, config('dms.language_whitelist')) ? trans('languages.' . $document->language) : trans('languages.no_language')}}
	</div>
	

	@if(!is_null($document->file))

	<div class="c-panel__meta">
		<div class="c-panel__label">{{trans('panels.meta.size')}}</div>
			
		{{KBox\Documents\Services\DocumentsService::human_filesize($document->file->size)}}
	</div>

	@endif

	@if(isset($properties) && method_exists($properties, 'toHtml'))
	
		{{ $properties }}

	@endif
	
</div>


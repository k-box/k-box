			
<div class="version__item">
	<div>
		<div class="version__title" title="{{$duplicate->duplicateOf->title}}">

			<a href="{{DmsRouting::preview($duplicate->duplicateOf)}}" target="_blank">{{$duplicate->duplicateOf->title}}</a>
		</div>

		<div class="version__meta">
		
			<div class="version__author">
				@if(!is_null($duplicate->duplicateOf->owner))
					{{ $duplicate->duplicateOf->owner->name }},&nbsp;
				@endif

				<span title="{{ localized_date_full($duplicate->duplicateOf->updated_at) }}">{{ localized_date_human_diff($duplicate->duplicateOf->updated_at) }}</span>
			</div>
			
			<div>
				
				@include('documents.partials.collections', [
					'document_is_trashed' => true,
					'user_can_edit_public_groups' => false,
					'user_can_edit_private_groups' => false,
					'document_id' =>  $duplicate->duplicateOf->id,
					'collections' => $collections,
					'use_groups_page' => $use_groups_page,
					'is_in_collection' => isset($is_in_collection) && $is_in_collection 
				])
			</div>

		</div>
	</div>
	
	
	{{-- <div>
		<button class="button button--ghost" data-action="restoreVersion" data-version-title="{{$duplicate->duplicateOf->name}}" data-document-id="{{ $document->id }}" data-version-id="{{ $duplicate->duplicateOf->uuid }}">{{ trans('actions.restore') }}</button>
	</div> --}}
	
</div>

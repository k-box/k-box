			
<div class="version__item">
	<div>
		<div class="version__title" title="{{$duplicate->duplicateOf->title}}">

			<a href="{{DmsRouting::preview($duplicate->duplicateOf)}}" target="_blank">{{$duplicate->duplicateOf->title}}</a>


			@if($duplicate->duplicateOf->trashed())
				&nbsp;({{ trans('documents.duplicates.in_trash') }})
			@endif
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
					'is_in_collection' => isset($is_in_collection) && $is_in_collection,
					'hide_empty_message' => true
				])
			</div>

		</div>
		@unless($duplicate->resolved || $duplicate->document->trashed())
		<div>
			<button class="button button--ghost" data-action="resolveDuplicate" data-duplicate-id="{{ $duplicate->id }}">{{ trans('documents.duplicates.resolve_duplicate_button') }}</button>
		</div>
		@endunless
	</div>
	
	
	
</div>

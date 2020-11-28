        
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

	<x-copy-button :links="[DmsRouting::preview($document, $version)]" class="my-4" />

	<div class="js-license-details">
		@include('documents.partials.license', ['license' => $document->copyright_usage, 'owner' => $document->copyright_owner])
	</div>
		
	@auth
		<div class="file-properties__property">
			<span class="file-properties__label">{{trans('panels.groups_section_title')}}</span>
			@include('documents.partials.collections', [
				'document_is_trashed' => $document->trashed(),
				'user_can_edit_public_groups' => false,
				'user_can_edit_private_groups' => false,
				'document_id' =>  $document->id,
				'collections' => $groups ?? null,
				'use_groups_page' => $use_groups_page ?? false,
				'is_in_collection' => $is_in_collection ?? false
				])
		</div>
	@endauth

	@if(!empty($document->abstract))

		<div class="meta abstract">
			<h4 class="c-panel__section">{{trans('panels.abstract_section_title')}}</h4>
			
			@component('components.markdown', ['class' => 'markdown--within bg-gray-100 p-1'])
				{!! $document->abstract_html !!}
			@endcomponent
		</div>

	@endif
	
	@include('documents.partials.properties')
	
</div>
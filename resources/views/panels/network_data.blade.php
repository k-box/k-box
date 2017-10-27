{{-- Panel for showing Search Data, expecting KSearch...Data instance   --}}

<div class="c-panel__header">
	
	<div>
		<h4 class="c-panel__title">{{ $item->properties->title }}</h4>

		<div class="is_public" title="{{trans('documents.descriptor.is_public_description')}}">

			@materialicon('social', 'public', 'button__icon'){{trans('documents.descriptor.is_public')}}

		</div>

	</div>

	<div class="c-panel__thumbnail">
	
		<img src="{{ $item->properties->thumbnail }}" />

	</div>

</div>

<div class="c-panel__actions">

	<a href="{{$item->url}}" class="button"  target="_blank">{{ trans('panels.open_site_btn') }} </a>

</div>

<div class="c-panel__data">

	@if(!empty($item->properties->abstract))

		<div class="meta abstract">
			<h4 class="c-panel__section">{{trans('panels.abstract_section_title')}}</h4>
			{{$item->properties->abstract}}
		</div>

	@endif

	<div class="meta info">

		<h4 class="c-panel__section">{{trans('panels.info_section_title')}}</h4>

		<div class="c-panel__meta">
			<div class="c-panel__label">
				{{trans('panels.meta.authors')}}
			</div>
			
			@if(!empty($item->author))

				{{ join(', ', array_pluck($item->author, 'name')) }}

			@endif
			
		</div>
		<div class="c-panel__meta">
			<div class="c-panel__label">
				{{trans('panels.meta.language')}}
			</div>
			
				{{!empty($item->properties->language) ? trans('languages.' . $item->properties->language) : trans('languages.no_language')}}
			</div>
		</div>
		<div class="c-panel__meta">
			<div class="c-panel__label">
				{{trans('panels.meta.added_on')}}
			</div>
			
				@if($item->properties->created_at instanceof \DateTime)
				
				 	{{\Carbon\Carbon::instance($item->properties->created_at)->toDateTimeString()}}

				@else
					{{$item->properties->created_at}}
				@endif
			
		</div>

		<div class="c-panel__meta">
			<div class="c-panel__label">
				{{trans('panels.meta.size')}}
			</div>
				
				{{Klink\DmsDocuments\DocumentsService::human_filesize($item->properties->size)}}

		</div>

		<div class="c-panel__meta">
			<div class="c-panel__label">
				{{trans('panels.meta.uploaded_by')}}
			</div>
			
				
				{{ $item->uploader->name }}

		</div>
		
	</div>
</div>

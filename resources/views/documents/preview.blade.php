@extends('global')


@section('header')

	

@overwrite




@push('properties-before')

	@if($is_user_logged)


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
        
    @endif

@endpush


@section('content')


	<div class="preview js-preview">
    
    <div class="preview__header">
	
		<a class="logo white" href="@if(isset( $is_user_logged ) && isset($current_user_home_route) && $is_user_logged){{$current_user_home_route}}@else{{route('frontpage')}}/@endif">
			@include('headers.logo')
		</a>
			

        <div class="preview__title-container">

            <span class="preview__title">{{ $document->title }}
			
				@if($version)
					&nbsp;/&nbsp;{{ $version->name }}
				@endif
			</span>            
        
        </div>

        <div class="preview__actions">
        
            <a class="preview__button js-preview-download-button"  href="{{DmsRouting::download($document, $version)}}" download="{{ $filename_for_download }}">{{ trans('panels.download_btn') }}</a>
            
            <button class="preview__button preview__button--expandable js-preview-details-button"><span class="preview__button-close">{{ trans('preview::actions.close') }}</span>{{ trans('preview::actions.details') }}</button>

        </div>
    
    </div>


    <div class="preview__body">

        <div class="preview__area js-preview-area">

            <div class="preview__content js-preview-content">



@if(!$file->canBePreviewed())

	<div class="disclaimer">

		<h4>{{trans('documents.preview.not_available')}}</h4>
	
	
		<a class="button button-primary" href="{{DmsRouting::download($document, $version)}}" target="_blank" download="{{ $filename_for_download }}">
			{{trans('panels.download_btn')}}
		</a>
	
	</div>

@elseif($document->isFileUploadComplete())

	@if($type=='image')
	
		<img src="{{DmsRouting::download($document, $version)}}" alt="{{$document->title}}">

	@elseif($type=='video')

		@if($document->status === \KBox\DocumentDescriptor::STATUS_COMPLETED)

			<video id="the-player" 
				data-dash="{{ route('video.play', ['uuid' => $document->file->uuid, 'resource' => 'mpd']) }}"
				data-source="{{ DmsRouting::download($document, $version) }}"
				data-source-type="{{ $document->mime_type }}"
				controls preload="none"
				poster="{{ DmsRouting::thumbnail($document, $version) }}">

			</video>

		@else 

			<div class="c-message c-message--warning">
				<p>{{ trans('documents.preview.video_not_ready') }}</p>
			</div>

		@endif
	
	@elseif($type=='document' && $extension === 'pdf')

		<iframe src="{{DmsRouting::embed($document, $version)}}" frameborder="0"></iframe>
	
	@elseif(isset($render) && !empty($render))
	
			{!!$render!!}
	
	@else
	
		<div class="preview__error">
	
			<h4>{{trans('documents.preview.error', ['document' => $document->title])}}</h4>
		
		
			<a class="button button-primary" href="{{DmsRouting::download($document, $version)}}" download="{{ $filename_for_download }}">
				{{trans('panels.download_btn')}}
			</a>
		
		</div>
	
	@endif

@else

	<div class="c-message c-message--warning">
		<p>{{ trans('documents.edit.not_fully_uploaded') }}</p>
		<p>{{ trans('documents.edit.preview_available_when_upload_completes') }}</p>
	</div>

@endif

            </div>
        </div>
    
        <div class="preview__sidebar js-preview-sidebar">
        
			@include('preview::partials.properties')
			
        </div>
    </div>


</div>
	

@stop

@section('scripts')

	<script>
	require(['modules/preview'], function(Preview){

		Preview.load();
	});
	</script>

@stop


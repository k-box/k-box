@extends('layout.full')

@section('breadcrumbs')
	<a href="{{route('documents.index')}}"  class="breadcrumb__item">{{trans('documents.page_title')}}</a> <span class="breadcrumb__item--current">{{$document->title}}</span>
@stop


@section('page')

@if($document->trashed())

<div class="is-trashed">

	{{trans('documents.descriptor.trashed')}}

</div>

@endif

@if(!$document->isMine())

<div class="is-trashed">

	{{trans('documents.descriptor.klink_public_not_mine')}}

</div>

@endif

<form action="{{route('documents.update', $document->id)}}" class="c-form edit-view" enctype="multipart/form-data" method="post" class="document-form" id="edit-form">

	<input type="hidden" name="document_descriptor" value="{{ $document->id }}"'>

	@if(!$document->trashed() && $document->isMine() && !$document->isFileUploadComplete())
	
	<div class="c-message c-message--warning">
		{!!trans('documents.edit.not_fully_uploaded')!!}
	</div>
	
	@endif

	{{ csrf_field() }}
	{{ method_field('PUT') }}

	@include('errors.list')

	@if(!$document->trashed() && $document->isMine() && !$document->isIndexed())
	
	<div class="c-message c-message--info">
		{!!trans('documents.messages.processing')!!}
	</div>
	
    @endif

	<div class="flex flex-col md:flex-row md:flex-wrap lg:flex-nowrap">
		<div class="pr-2 mt-4 mb-4 lg:mb-0 w-full md:w-1/2 lg:w-1/3 xl:w-2/4">
	
			@if( isset($errors) && $errors->has('title') )
				<span class="field-error">{{ implode(",", $errors->get('title'))  }}</span>
			@endif
			<input type="text" placeholder="{{trans('documents.edit.title_placeholder')}}" title="{{trans('documents.edit.title_placeholder')}}" name="title" value="{{old('title', isset($document) ? $document->title : '')}}" class="form-input w-full lg:w-10/12 text-lg" @if(!$document->isMine() || !$can_edit_document) disabled @endif> 

			<div class="mt-1 mb-4 form-description ">

				<span class="badge">{{$document->document_type}}</span>

				@if($document->isRemoteWebPage() && !is_null($document->file))

					{{$document->file->original_uri}}

				@else

					{{$document->document_uri}}

				@endif

			</div>
	
			@if( isset($errors) && $errors->has('document') )
				<span class="field-error">{{ implode(",", $errors->get('document'))  }}</span>
			@endif
	
			<div class=" mb-4 meta collections">
				<label class="font-bold">{{trans('panels.groups_section_title')}}</label>
	
				@include('documents.partials.collections', [
					'document_is_trashed' => true,
					'user_can_edit_public_groups' => false,
					'user_can_edit_private_groups' => false,
					'document_id' =>  $document->id,
					'collections' => $groups,
					'use_groups_page' => $use_groups_page,
					'is_in_collection' => isset($is_in_collection) && $is_in_collection 
				])
	
			</div>
	
			<div class=" mb-4">
				<label for="abstract" class="font-bold">{{trans('documents.edit.abstract_label')}}</label>
				@if( isset($errors) && $errors->has('abstract') )
		            <span class="field-error">{{ implode(",", $errors->get('abstract'))  }}</span>
		        @endif
	  			<textarea class="form-textarea block w-full lg:w-10/12 form-input--height-3" placeholder="{{trans('documents.edit.abstract_placeholder')}}" id="abstract" name="abstract" @if(!$document->isMine() || !$can_edit_document) disabled @endif>{{old('abstract', isset($document) ? $document->abstract : '')}}</textarea>
				<span class="description">{!! trans('documents.edit.abstract_help') !!}</span>
			</div>
			<div class=" mb-4">
	  			<label for="authors" class="font-bold">{{trans('documents.edit.authors_label')}}</label>
				<p class="description">{!!trans('documents.edit.authors_help')!!}</p>
	  			@if( isset($errors) && $errors->has('authors') )
		            <span class="field-error">{{ implode(",", $errors->get('authors'))  }}</span>
		        @endif
	  			<textarea class="form-textarea block w-full lg:w-10/12" @if(!$document->isMine() || !$can_edit_document) disabled @endif placeholder="{{trans('documents.edit.authors_placeholder')}}" id="authors" name="authors">{{old('authors', isset($document) ? $document->authors : '')}}</textarea>
			</div>
				
			<div class=" mb-4">
	  			<label for="language" class="font-bold">{{trans('documents.edit.language_label')}}</label>
	  			@if( isset($errors) && $errors->has('language') )
		            <span class="field-error">{{ implode(",", $errors->get('language'))  }}</span>
		        @endif
				<select class="form-select block w-2/3" id="language" name="language" @if(!$document->isMine() || !$can_edit_document) disabled @endif>
				<option value="__" @if($document->language == '__' || !$document->language || !in_array($document->language, config('dms.language_whitelist'))) selected @endif>{{trans('languages.no_language')}}</option>
				<option value="en" @if($document->language == 'en') selected @endif>{{trans('languages.en')}}</option>
				<option value="ru" @if($document->language == 'ru') selected @endif>{{trans('languages.ru')}}</option>
				<option value="tg" @if($document->language == 'tg') selected @endif>{{trans('languages.tg')}}</option>
				<option value="ky" @if($document->language == 'ky') selected @endif>{{trans('languages.ky')}}</option>
				<option value="de" @if($document->language == 'de') selected @endif>{{trans('languages.de')}}</option>
				<option value="fr" @if($document->language == 'fr') selected @endif>{{trans('languages.fr')}}</option>
				<option value="it" @if($document->language == 'it') selected @endif>{{trans('languages.it')}}</option>
				
				</select>
			</div>
	
				
			@include('documents.partials.copyrightform', [
				'selected_license' => $document->copyright_usage,
				'owner' => $document->copyright_owner])
	
	
		</div>
	
		<div class="pr-2 mt-4 mb-4 lg:mb-0 w-full md:w-1/2 lg:w-1/3 xl:w-1/4">
	
			@if($document->isMine() && $can_edit_document)
				<button type="submit" class="button button--primary button--larger ladda-button save-button">
					<span class="normal">{{trans('actions.save')}}</span>
					<span class="processing">{{trans('actions.saving')}}</span>
				</button>
			@endif

			@if($document->isMine() && ($can_share || $can_make_public && network_enabled()))
				<div class="mt-2 mb-4">
					<button class="button js-open-share-dialog" data-id="{{$document->id}}">@materialicon('social','people', 'button__icon mr-1'){{ trans('panels.sharing_settings_btn') }}</button>
				</div>
			@endif
			

			<div class=" mb-4">
				<div>
					{!! trans('documents.edit.last_edited', ['time' => $document->updated_at->render(true)]) !!}
				</div>

				<div>
					{!! trans('documents.edit.created_on', ['time' => $document->created_at->render(true)]) !!}
				</div>
				
				@if($document->isFileUploadComplete())

					<div>
						<span>
							@can('see_uploader', $document->file)
								{!! trans('documents.edit.uploaded_by', ['name' => optional($document->file->user)->name ?? '' ]) !!}
							@elsecan('see_owner', $document)
								{!! trans('documents.edit.uploaded_by', ['name' => e($document->user_uploader)]) !!}
							@else 
								@component('components.undisclosed_user')
									
								@endcomponent
							@endcan
						</span>
					</div>
				@endif

			</div>

			<div class=" mb-4 flex items-center flex-wrap">
				
				@if(!$document->isRemoteWebPage())

					@if($document->isFileUploadComplete())

						<a href="{{DmsRouting::preview($document)}}" class="button mb-2 mr-2">{!!trans('panels.open_btn')!!} </a>

						<x-copy-button :links="[DmsRouting::preview($document)]" class="mb-2" />

					@endif

					@if($document->isFileUploadComplete())
						<a href="{{DmsRouting::download($document)}}" target="_blank" download="{{ $document->title }}" class="button mb-2">
							{{trans('panels.download_btn')}} 
							({{KBox\Documents\Services\DocumentsService::extension_from_file($document->file)}}, {{KBox\Documents\Services\DocumentsService::human_filesize($document->file->size)}})
						</a>
					@endif

				@elseif(!is_null($document->file))

					<a href="{{$document->file->original_uri}}" class="button" tarrget="_blank">{!!trans('panels.open_site_btn')!!} </a>

				@endif
			</div>
	
			
	
			<div class=" mb-4  mb-4--thumbnail">
	
				<img src="{{DmsRouting::thumbnail($document)}}" />
			</div>
	
		</div>
	
	
		<div class="mt-4 mb-4 lg:mb-0 w-full lg:w-1/3 xl:w-1/4">
	
			@includeWhen( isset($duplicates) && ! $duplicates->isEmpty() , 'documents.partials.duplicates')
	
				@if($document->isFileUploadComplete())
				@include('documents.partials.versioninfo')
				@endif
		</div>
	</div>


</form>

@stop

@section('panels')

@include('panels.generic')

@stop

@section('scripts')

	<script>
	require(['modules/editdocument'], function(Panels){
	});
	</script>

@stop
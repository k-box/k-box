@extends('management-layout')

@section('sub-header')

	
		
		<a href="{{route('documents.index')}}" class="parent">{{trans('documents.page_title')}}</a> {{$document->title}}



@stop


@section('action-menu')



@stop


@section('content')

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

<form action="{{route('documents.update', $document->id)}}" enctype="multipart/form-data" method="post" class="document-form" id="edit-form">

	@if($document->isMine() && !$document->isIndexed())
	
	<div class="alert info">
		{!!trans('documents.edit.not_index_message')!!}
	</div>
	
	@endif

	<input type="hidden" name="_token" value="{{{ csrf_token() }}}"> 
	<input type="hidden" name="_method" value="put">

	@include('errors.list')

	<div class="row">

		<div class="five columns ">

			<div>
				@if( $errors->has('title') )
		            <span class="field-error">{{ implode(",", $errors->get('title'))  }}</span>
		        @endif
				<input type="text" placeholder="{{trans('documents.edit.title_placeholder')}}" title="{{trans('documents.edit.title_placeholder')}}" name="title" value="{{old('title', isset($document) ? $document->title : '')}}" class="title" @if(!$document->isMine() || !$can_edit_document) disabled @endif> 

				<div class="description">

					<span class="badge klink-{{$document->document_type}}">{{$document->document_type}}</span>

					@if($document->isRemoteWebPage() && !is_null($document->file))

						{{$document->file->original_uri}}

					@else

						{{$document->document_uri}}

					@endif

				</div>
			</div>

			@if( $errors->has('document') )
	            <span class="field-error">{{ implode(",", $errors->get('document'))  }}</span>
	        @endif


<div class="meta collections">
			<label>{{trans('panels.groups_section_title')}}</label>

			@if(isset($is_in_collection) && $is_in_collection)


				@foreach($groups as $group)

					<div class="badge" @if($group->color) data-color="{{$group->color}}" @endif>
						
						{{$group->name}}
						
					</div>

				@endforeach

			@else

				<p>{{trans('panels.not_in_collection')}}</p>

			@endif

		</div>


			<label for="abstract">{{trans('documents.edit.abstract_label')}}</label>
			@if( $errors->has('abstract') )
	            <span class="field-error">{{ implode(",", $errors->get('abstract'))  }}</span>
	        @endif
  			<textarea class="u-full-width" placeholder="{{trans('documents.edit.abstract_placeholder')}}" id="abstract" name="abstract" @if(!$document->isMine() || !$can_edit_document) disabled @endif>
{{old('abstract', isset($document) ? $document->abstract : '')}}</textarea>

  			<label for="authors">{{trans('documents.edit.authors_label')}}</label>
			<p class="description">{!!trans('documents.edit.authors_help')!!}</p>
  			@if( $errors->has('authors') )
	            <span class="field-error">{{ implode(",", $errors->get('authors'))  }}</span>
	        @endif
  			<textarea class="u-full-width" @if(!$document->isMine() || !$can_edit_document) disabled @endif placeholder="{{trans('documents.edit.authors_placeholder')}}" id="authors" name="authors">
{{old('authors', isset($document) ? $document->authors : '')}}</textarea>
			

  			<label for="language">{{trans('documents.edit.language_label')}}</label>
  			@if( $errors->has('language') )
	            <span class="field-error">{{ implode(",", $errors->get('language'))  }}</span>
	        @endif
			<select class="u-full-width" id="language" name="language" @if(!$document->isMine() || !$can_edit_document) disabled @endif>
			<option value="en" @if($document->language == 'en') selected @endif>{{trans('languages.en')}}</option>
			<option value="ru" @if($document->language == 'ru') selected @endif>{{trans('languages.ru')}}</option>
			<option value="kg" @if($document->language == 'kg') selected @endif>{{trans('languages.kg')}}</option>
			<option value="de" @if($document->language == 'de') selected @endif>{{trans('languages.de')}}</option>
			<option value="fr" @if($document->language == 'fr') selected @endif>{{trans('languages.fr')}}</option>
			<option value="it" @if($document->language == 'it') selected @endif>{{trans('languages.it')}}</option>
			
			</select>

			

			

		</div>

		<div class="three columns">

			<div class="form-actions">

				@if($document->isMine() && $can_edit_document)
					<button type="submit" class="button-primary ladda-button">
						<span class="normal">{{trans('actions.save')}}</span>
						<span class="processing">{{trans('actions.saving')}}</span>
					</button>
				@endif

				@if($document->isMine() && $can_make_public)

				<div>
					<input type="checkbox" name="visibility" id="visibility" value="public" @if($document->isPublic()) checked @endif>
					<label for="visibility">{{trans('actions.make_public')}}</label>
					<span class="description @if($document->isPrivate()) hidden @endif">{{trans('documents.edit.public_visibility_description')}}</span>
				</div>

				@endif
				

				<div class="minimeta">

					<p>{!! trans('documents.edit.last_edited', ['time' => $document->updated_at->diffForHumans()]) !!}</p>

					<p>{!! trans('documents.edit.created_on', ['time' => $document->created_at->toDayDateTimeString()]) !!}</p>

					@if(!is_null($document->file))
					<p>{!! trans('documents.edit.uploaded_by', ['name' => $document->file->user->name]) !!}</p>
					@endif

				</div>

				<div>
					
					@if(!$document->isRemoteWebPage())

						@if(!is_null($document->file))

							<a href="{{DmsRouting::preview($document)}}" class="button">{!!trans('panels.open_btn')!!} </a>

						@endif

						<a href="{{DmsRouting::download($document)}}" target="_blank" download="{{ $document->title }}" class="button">
							{{trans('panels.download_btn')}} 

							@if(!is_null($document->file))
								({{Klink\DmsDocuments\DocumentsService::extension_from_file($document->file)}}, {{Klink\DmsDocuments\DocumentsService::human_filesize($document->file->size)}})
							@endif
						</a>

					@else 

						@if(!is_null($document->file))

							<a href="{{$document->file->original_uri}}" class="button" tarrget="_blank">{!!trans('panels.open_site_btn')!!} </a>

						@endif

					@endif
				</div>

			</div>

			

			<div class="thumbnail">
	
				<img src="{{DmsRouting::thumbnail($document)}}" />
			</div>

			

		</div>

		<div class="four columns">

			@if(!is_null($document->file))
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
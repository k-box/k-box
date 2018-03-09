<!-- Document panel -->

<!-- Expecting:
	DocumentDescriptor instance
	panel_id, if no id is considered as template
 -->

<div class="c-panel__header">
	
	<div>
		<h4 class="c-panel__title">{{ $item->title }}</h4>
			

		@if(isset($stars_count) && !$item->trashed())

			<div class="stars">

				@materialicon('toggle', 'star', 'button__icon'){{trans_choice('starred.starred_count_alt', $stars_count, ['number' => $stars_count])}}

			</div>

		@endif

		


		@if($item->is_public)

			<div class="is_public" title="{{trans('documents.descriptor.is_public_description')}}">

				@materialicon('social', 'public', 'button__icon'){{trans('documents.descriptor.is_public')}}

			</div>

		@endif
	</div>

	<div class="c-panel__thumbnail">
	
		<img src="{{DmsRouting::thumbnail($item)}}" />

	</div>

</div>
@if($item->isFileUploadComplete())
<div class="c-panel__actions">

    
    <?php $real_download_link = null; ?>
    <?php $real_preview_link = null; ?>
	
	@if(!$item->trashed())

		@if(!$item->isRemoteWebPage() && !starts_with($item->document_uri, 'http://msri-hub.ucentralasia.org/') && !starts_with($item->document_uri, 'http://staging-uca.cloudapp.net/'))
	
			@if(!is_null($item->file) )
	           <?php $real_preview_link = DmsRouting::preview($item); ?>
				<a href="{{DmsRouting::preview($item)}}" class="button button--primary" target="_blank">{!!trans('panels.open_btn')!!} </a>
	
			@endif
	       
           <?php $real_download_link = DmsRouting::download($item); ?>
           
			<a href="{{DmsRouting::download($item)}}" target="_blank" class="button">
				{{trans('panels.download_btn')}} 
	
				@if(!is_null($item->file))
					({{Klink\DmsDocuments\DocumentsService::extension_from_file($item->file)}}, {{Klink\DmsDocuments\DocumentsService::human_filesize($item->file->size)}})
				@endif
			</a>
		@elseif(starts_with($item->document_uri, 'http://msri-hub.ucentralasia.org/') || starts_with($item->document_uri, 'http://staging-uca.cloudapp.net/'))
		      <?php $real_preview_link = $item->document_uri; ?>
			<a href="{{$item->document_uri}}" class="button"  target="_blank">{!!trans('panels.open_site_btn')!!} </a>
		
		@else 
	
			@if(!is_null($item->file))
	            <?php $real_preview_link = $item->file->original_uri; ?>
				<a href="{{$item->file->original_uri}}" class="button" target="_blank">{!!trans('panels.open_site_btn')!!} </a>
	
			@endif
	
	
		@endif
	
		@if(isset($user_can_edit) && $user_can_edit)
	
			<a href="{{route('documents.edit', $item->id)}}" class="button" title="{{trans('panels.edit_btn_title')}} ">
				@materialicon('content', 'create', 'button__icon')
				{{trans('panels.edit_btn')}}
			</a>

			<a href="{{route('documents.edit', $item->id)}}" class="button" title="{{trans('panels.version_btn_title')}} ">
				@materialicon('action', 'history', 'button__icon')
				{{trans('panels.version_btn')}} 
			</a>			
	
		@endif

	@else
	
		<a href="#" class="button" data-action="restore" data-id="{{$item->id}}" title="{{trans('panels.restore_btn_title')}}">{{trans('panels.restore_btn')}}</a>
	
	@endif

</div>
@else 

<div class="c-message c-message--warning">
	{{ trans('documents.edit.not_fully_uploaded') }}
</div>

@endif

<div class="c-panel__data">

		@include('documents.partials.license', ['license' => $item->copyright_usage, 'owner' => $item->copyright_owner])

@if(!empty($item->abstract))

<div class="meta abstract">
	<h4 class="c-panel__section">{{trans('panels.abstract_section_title')}}</h4>
	{{$item->abstract}}
</div>

@endif

	@if($is_user_logged)

		<div class="meta collections">
			<h4 class="c-panel__section">{{trans('panels.groups_section_title')}}</h4>
			
			@include('documents.partials.collections', [
				'document_is_trashed' => $item->trashed(),
				'user_can_edit_public_groups' => $user_can_edit_public_groups,
				'user_can_edit_private_groups' => $user_can_edit_private_groups,
				'document_id' =>  $item->id,
				'collections' => $groups,
				'use_groups_page' => $use_groups_page,
				'is_in_collection' => isset($is_in_collection) && $is_in_collection 
			])

		</div>
        
    @endif
    
    @if($is_user_logged && $item->isMine())

		<div class="meta share">
			<h4 class="c-panel__section">{{trans('panels.share_section_title')}}</h4>

			<p style="margin-bottom:8px">
				<a href="#" data-id="{{$item->id}}" @if($can_share) data-action="openShareDialogWithAccess" @endif>{{ trans('share.dialog.document_is_shared') }}</a>
				
				@foreach($access as $line)
					<br/>- {{ $line }}
				@endforeach
				
			</p>

			@if($can_share)
			<button class="button js-open-share-dialog" data-id="{{$item->id}}" data-action="openShareDialog">@materialicon('social','people', 'button__icon'){{ trans('panels.sharing_settings_btn') }}</button>
			@endif

		</div>

		

	@endif

	<div class="meta info">

		<h4 class="c-panel__section">{{trans('panels.info_section_title')}}</h4>


		<div class="c-panel__meta">
			<div class="c-panel__label">
				{{trans('panels.meta.authors')}}
			</div>
			
				
				{{$item->authors}}

			
		</div>
		<div class="c-panel__meta">
			<div class="c-panel__label">
				{{trans('panels.meta.main_contact')}}
			</div>
			
				
				{{$item->user_owner}}

			
		</div>
		<div class="c-panel__meta">
			<div class="c-panel__label">
				{{trans('panels.meta.language')}}
			</div>
			
				{{!empty($item->language) ? trans('languages.' . $item->language) : trans('languages.no_language')}}
			</div>
		</div>
		<div class="c-panel__meta">
			<div class="c-panel__label">
				{{trans('panels.meta.added_on')}}
			</div>
			
				
				{{$item->getCreatedAt(true)}}

			
		</div>

		@if($item->trashed())

		<div class="c-panel__meta">
			<div class="c-panel__label">
				{{trans('panels.meta.deleted_on')}}
			</div>
			
				
				{{$item->getDeletedAt(true)}}

			
		</div>

		@endif

		@if(!is_null($item->file))

		<div class="c-panel__meta">
			<div class="c-panel__label">
				{{trans('panels.meta.size')}}
			</div>
			
				
				{{Klink\DmsDocuments\DocumentsService::human_filesize($item->file->size)}}

		</div>

		@endif


		

		<div class="c-panel__meta">
			<div class="c-panel__label">
				{{trans('panels.meta.uploaded_by')}}
			</div>
			
			@if($item->owner)

				{{ $item->owner->name }}

				@if($item->owner->organization_name)
					@if($item->owner->organization_website)

						(<a href="{{$item->owner->organization_website}}">{{ $item->owner->organization_name}}</a>)

					@else

						({{ $item->owner->organization_name}})

					@endif
				@endif
			
			@else
				
				{{ $item->user_uploader }}

			@endif

		</div>
		
	</div>
</div>

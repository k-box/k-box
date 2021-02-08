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

				<x-star-button :starID="$star_id" :documentID="$item->local_document_id" :count="$stars_count" />

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

		@if(!$item->isRemoteWebPage() && !\Illuminate\Support\Str::startsWith($item->document_uri, 'http://msri-hub.ucentralasia.org/') && !\Illuminate\Support\Str::startsWith($item->document_uri, 'http://staging-uca.cloudapp.net/'))
	
			@if(!is_null($item->file) )
	           <?php $real_preview_link = DmsRouting::preview($item); ?>
				<a href="{{ $real_preview_link }}" class="button button--primary" target="_blank">{!!trans('panels.open_btn')!!} </a>
	
				<x-copy-button :links="[$real_preview_link]" />
			@endif
	       
           <?php $real_download_link = DmsRouting::download($item); ?>
           
			<a href="{{DmsRouting::download($item)}}" target="_blank" class="button">
				{{trans('panels.download_btn')}} 
	
				@if(!is_null($item->file))
					({{KBox\Documents\Services\DocumentsService::extension_from_file($item->file)}}, {{KBox\Documents\Services\DocumentsService::human_filesize($item->file->size)}})
				@endif
			</a>
		@elseif(\Illuminate\Support\Str::startsWith($item->document_uri, 'http://msri-hub.ucentralasia.org/') || \Illuminate\Support\Str::startsWith($item->document_uri, 'http://staging-uca.cloudapp.net/'))
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
				@materialicon('content', 'create', 'button__icon mr-1')
				{{trans('panels.edit_btn')}}
			</a>

			
			@if( isset($badge_duplicate) && $badge_duplicate )
		
				<a href="{{route('documents.edit', $item->id)}}" class="button" title="{{trans('documents.duplicates.duplicates_btn_hint')}} ">
					@materialicon('content', 'content_copy', 'button__icon mr-1')
					{{trans('documents.duplicates.duplicates_btn')}}
				</a>	
		
			@endif

			<a href="{{route('documents.edit', $item->id)}}" class="button" title="{{trans('panels.version_btn_title')}} ">
				@materialicon('action', 'history', 'button__icon mr-1')
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

	<x-markdown class="markdown--within bg-gray-100 p-1">{!! $item->abstract !!}</x-markdown>
	
</div>

@endif

	@auth

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
        
    @endauth
    
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
			<button class="button js-open-share-dialog" data-id="{{$item->id}}" data-action="openShareDialog">@materialicon('social','people', 'button__icon mr-1'){{ trans('panels.sharing_settings_btn') }}</button>
			@endif

		</div>

	@endif

	@include('documents.partials.properties', ['document' => $item])

</div>

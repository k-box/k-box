<!-- Document panel -->

<!-- Expecting:
	DocumentDescriptor instance
	panel_id, if no id is considered as template
 -->

<a href="#close" title="{{trans('panels.close_btn')}}" class="close icon-navigation-white icon-navigation-white-ic_close_white_24dp"></a>



<div class="header">

	@if($item->trashed())

	<div class="is-trashed">

		{{trans('documents.descriptor.trashed')}}

	</div>

	@endif

	<div class="thumbnail">
	
		<img src="{{DmsRouting::thumbnail($item)}}" />

	</div>
	
	<div class="column">

		<h4 class="title">{{ $item->title }}</h4>
		

			@if(isset($stars_count) && !$item->trashed())

			<div class="stars">

				<span class="icon-toggle-white icon-toggle-white-ic_star_white_24dp"></span>{{trans_choice('starred.starred_count_alt', $stars_count, ['number' => $stars_count])}}

			</div>

			@endif


			@if($item->is_public)

			<div class="is_public" title="{{trans('documents.descriptor.is_public_description')}}">

				<span class="icon-social-white icon-social-white-ic_public_white_24dp"></span>{{trans('documents.descriptor.is_public')}}

			</div>

			@endif

	</div>

</div>
<div class="actions">
    
    <?php $real_download_link = null; ?>
    <?php $real_preview_link = null; ?>
	
	@if(!$item->trashed())

		@if(!$item->isRemoteWebPage() && !starts_with($item->document_uri, 'http://msri-hub.ucentralasia.org/') && !starts_with($item->document_uri, 'http://staging-uca.cloudapp.net/'))
	
			@if(!is_null($item->file) )
	           <?php $real_preview_link = DmsRouting::preview($item); ?>
				<a href="{{DmsRouting::preview($item)}}" class="button" target="_blank">{!!trans('panels.open_btn')!!} </a>
	
			@endif
	       
           <?php $real_download_link = DmsRouting::download($item); ?>
           
			<a href="{{DmsRouting::download($item)}}" target="_blank" download="{{ $item->title }}" class="button">
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
	
			<div class="u-pull-right">
	
				<a href="{{route('documents.edit', $item->id)}}" class="button" title="{{trans('panels.edit_btn_title')}} ">
					<span class="btn-icon icon-content-black icon-content-black-ic_create_black_24dp"></span>
					{{trans('panels.edit_btn')}} 
				</a>
	
				<a href="{{route('documents.edit', $item->id)}}" class="button" title="{{trans('panels.version_btn_title')}} ">
					<span class="btn-icon icon-action-black icon-action-black-ic_history_black_24dp"></span>
					{{trans('panels.version_btn')}} 
				</a>
	
			</div>
	
		@endif

	@else
	
		<a href="#" class="button" data-action="restore" data-id="{{$item->id}}" title="{{trans('panels.restore_btn_title')}}">{{trans('panels.restore_btn')}}</a>
	
	@endif

	
</div>

@if(!empty($item->abstract))

<div class="meta abstract">
	<h6 class="title">{{trans('panels.abstract_section_title')}}</h6>
	{{$item->abstract}}
</div>

@endif

	@if($is_user_logged)

		<div class="meta collections">
			<h6 class="title">{{trans('panels.groups_section_title')}}</h6>
			
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
			<h6 class="title">{{trans('panels.share_section_title')}}</h6>

			@if(isset($badge_shared) && $badge_shared)

				@forelse($shared_by_me as $share)

					@include('share.info', ['share' => $share])

				@empty

					<p>{{trans('panels.not_shared')}}</p>

				@endforelse



			@else

				<p>{{trans('panels.not_shared')}}</p>

			@endif
            

            @if( !is_null( $real_download_link ) || !is_null( $real_preview_link ) )

                <div class="copy-links">
                    <h6 class="title">{{ trans('share.share_link_section') }}</h6>
                        
					@if( !is_null( $real_preview_link ) )
					
						<div class="copy-link">
							<button class="copy-link__button js-clipboard-btn" data-clipboard-target="#document_link">
								<span class="copy-link__button__normal">
									<span class="btn-icon icon-content-black icon-content-black-ic_content_copy_black_24dp"></span>
									{{ trans('share.document_link_copy') }}
								</span>
								<span class="copy-link__button__success">{{ trans('actions.clipboard.copied_title') }}</span>
								<span class="copy-link__button__error">{{ trans('actions.clipboard.not_copied_title') }}</span>
							</button>
						
							<input id="document_link" class="copy-link__input" readonly value="{{ $real_preview_link }}">
						

							<div class="copy-link__message copy-link__message--error js-copy-message-error">{{trans('actions.clipboard.not_copied_link_text')}}</div>
						</div>
					
					@endif
                
                </div>
            
            @endif

		</div>

		

	@endif

	<div class="meta info">

		<h6 class="title">{{trans('panels.info_section_title')}}</h6>


		<div class="row">
			<div class="three columns label">
				{{trans('panels.meta.authors')}}
			</div>
			<div class="nine colums">
				
				{{$item->authors}}

			</div>
		</div>
		<div class="row">
			<div class="three columns label">
				{{trans('panels.meta.institution')}}
			</div>
			<div class="nine colums">
				@if(!is_null($item->institution))
					{{$item->institution->name}}
				@endif
			</div>
		</div>
		<div class="row">
			<div class="three columns label">
				{{trans('panels.meta.main_contact')}}
			</div>
			<div class="nine colums">
				
				{{$item->user_owner}}

			</div>
		</div>
		<div class="row">
			<div class="three columns label">
				{{trans('panels.meta.language')}}
			</div>
			<div class="nine colums">
				{{!empty($item->language) ? trans('languages.' . $item->language) : trans('languages.no_language')}}
			</div>
		</div>
		<div class="row">
			<div class="three columns label">
				{{trans('panels.meta.added_on')}}
			</div>
			<div class="nine colums">
				
				{{$item->getCreatedAt(true)}}

			</div>
		</div>

		@if($item->trashed())

		<div class="row">
			<div class="three columns label">
				{{trans('panels.meta.deleted_on')}}
			</div>
			<div class="nine colums">
				
				{{$item->getDeletedAt(true)}}

			</div>
		</div>

		@endif

		@if(!is_null($item->file))

		<div class="row">
			<div class="three columns label">
				{{trans('panels.meta.size')}}
			</div>
			<div class="nine colums">
				
				{{Klink\DmsDocuments\DocumentsService::human_filesize($item->file->size)}}

			</div>
		</div>

		@endif

		<div class="row">
			<div class="three columns label">
				{{trans('panels.meta.uploaded_by')}}
			</div>
			<div class="nine colums">
				
				{{ $item->user_uploader }}

			</div>
		</div>
		
	</div>

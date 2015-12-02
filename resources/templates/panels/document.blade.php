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
	
	@if(!$item->trashed())

		@if(!$item->isRemoteWebPage() && !starts_with($item->document_uri, 'http://msri-hub.ucentralasia.org/') && !starts_with($item->document_uri, 'http://staging-uca.cloudapp.net/'))
	
			@if(!is_null($item->file) )
	
				<a href="{{DmsRouting::preview($item)}}" class="button" target="_blank">{!!trans('panels.open_btn')!!} </a>
	
			@endif
	
			<a href="{{DmsRouting::download($item)}}" target="_blank" download="{{ $item->title }}" class="button">
				{{trans('panels.download_btn')}} 
	
				@if(!is_null($item->file))
					({{Klink\DmsDocuments\DocumentsService::extension_from_file($item->file)}}, {{Klink\DmsDocuments\DocumentsService::human_filesize($item->file->size)}})
				@endif
			</a>
		@elseif(starts_with($item->document_uri, 'http://msri-hub.ucentralasia.org/') || starts_with($item->document_uri, 'http://staging-uca.cloudapp.net/'))
		
			<a href="{{$item->document_uri}}" class="button"  target="_blank">{!!trans('panels.open_site_btn')!!} </a>
		
		@else 
	
			@if(!is_null($item->file))
	
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

	@if($is_user_logged && $item->isMine())

		<div class="meta collections">
			<h6 class="title">{{trans('panels.groups_section_title')}}</h6>

			@if(isset($is_in_collection) && $is_in_collection)


				@foreach($groups as $group)

					<div class="badge" @if($group->color) data-color="{{$group->color}}" @endif>
						<a href="{{route( $use_groups_page ? 'documents.groups.show' : 'shares.group' , $group->id)}}" class="badge-link" title="{{ trans('panels.collection_open', ['collection' => $group->name ])}}">
							{{$group->name}}
						</a>
						@if(!$item->trashed() && (($group->is_private && $user_can_edit_private_groups) || (!$group->is_private && $user_can_edit_public_groups)))
							<a href="#remove" data-action="removeGroup" data-group-id="{{$group->id}}" data-document-id="{{$item->id}}" class="badge-remove" title="{{ trans('panels.collection_remove', ['collection' => $group->name ])}}">
								X
							</a>
						@endif
					</div>

				@endforeach

			@else

				<p>{{trans('panels.not_in_collection')}}</p>

			@endif

		</div>

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
				
				{{$item->institution->name}}

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
				
				{{trans('languages.' . $item->language)}}

			</div>
		</div>
		<div class="row">
			<div class="three columns label">
				{{trans('panels.meta.added_on')}}
			</div>
			<div class="nine colums">
				
				{{$item->created_at}}

			</div>
		</div>

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

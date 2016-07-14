
@if(class_basename(get_class($item)) === 'Group')

@include('groups.group')

@elseif (class_basename(get_class($item)) === 'DocumentDescriptor')

<div draggable="true" class="item @if(!isset($hide_checkboxes)) selectable @endif" rv-on-click="select" data-id="{{$item->id}}" data-class="document" data-institution="{{$item->institution_id}}" data-visibility="{{$item->visibility}}" data-type="{{$item->document_type}}" @if(isset($star_id)) data-star-id="{{$star_id}}" @endif @if(isset($share_id)) data-shareid="{{$share_id}}" @endif @if(isset($shared_with)) data-sharewith="true" @endif>

	@if(!isset($hide_checkboxes))

	<span class="selection-area"></span>

	<div class="selection">
		
		<span class="selection-tab">
			
		</span>

		<span class="select-box">
			
			<input type="checkbox" data-action="selectable" role="presentation" tabindex="-1" id="item-document-{{$item->id}}" class="checkbox">

		</span>

	</div>

	@endif

	<div class="icon" title="{{$item->document_type}}">
		<span class="klink-document-icon klink-{{$item->document_type}}"></span>
	</div>

	<div class="badges">
		
		@if( isset($badge_public) && $badge_public )
		
			<div class="badge public" title="{{trans('documents.descriptor.is_public')}}">
				<span class="icon-social-black icon-social-black-ic_public_black_24dp"></span>
			</div>
		
		@elseif( isset($badge_private) && $badge_private )

			<div class="badge private" title="{{trans('documents.descriptor.private')}}">
				<span class="icon-action-black icon-action-black-ic_lock_black_24dp"></span>
			</div>

		@endif
		
		@if( isset($badge_shared) && $badge_shared )

			<div class="badge shared" title="{{trans('documents.descriptor.shared')}}">
				<span class="icon-social-black icon-social-black-ic_people_black_24dp"></span>
			</div>

		@endif

		@if( isset($badge_error) && $badge_error )

			<div class="badge error" title="{{trans('documents.descriptor.indexing_error')}}">
				<span class="icon-action-black icon-action-black-ic_report_problem_black_24dp"></span>
			</div>

		@endif
		

	</div>

	

	<div class="thumbnail klink-{{$item->document_type}}">
		
		<img src="{{asset('images/unknown.png')}}" data-src="{{ DmsRouting::thumbnail($item)}}" />

	</div>

	<h2 class="title">

		@if( !$item->trashed() && isset($is_starrable) && $is_starrable)
			@if( $is_starred )
				<a href="#star" data-action="star" class="star @if($item->isPublic()) public @endif active" title="{{trans('starred.remove')}}" data-id="{{$star_id}}" data-inst="{{$item->institution->klink_id}}" data-doc="{{$item->local_document_id}}" data-visibility="{{$item->visibility}}">

				</a>
			@else
				<a href="#star" data-action="star" class="star @if($item->isPublic()) public @endif" title="{{trans('starred.add')}}"  data-visibility="{{$item->visibility}}" data-inst="{{$item->institution->klink_id}}" data-doc="{{$item->local_document_id}}" data-visibility="{{$item->visibility}}"></a>
			@endif
		@endif

		<a href="{{route('documents.show', $item->id)}}" class="link" target="_blank" title="{{ $item->title }}">
			{{ $item->title }}
		</a>
		
	</h2>

	<div class="meta">

		@if(!isset($share_id))
		<span class="meta-info institution-name" title="{{trans('documents.descriptor.owned_by')}}">
			<span class="meta-label">{{trans('documents.descriptor.owned_by')}}&nbsp;</span>{{$item->institution->name}}
		</span>
		@endif
		
		@if(isset($share_id) && isset($shared_with))
			<span class="meta-info shared-with" title="{{trans('share.shared_with_label')}}">
				<span class="meta-label">{{trans('share.shared_with_label')}}&nbsp;</span>@include('share.with', ['with_who' => $shared_with])
			</span>			
		@endif
		
		@if(isset($share_id) && isset($shared_by))
			<span class="meta-info shared-by" title="{{trans('share.shared_by_label')}}">
				<span class="meta-label">{{trans('share.shared_by_label')}}&nbsp;</span>@include('share.with', ['with_who' => $shared_by])
			</span>			
		@endif
		
		

		
		<span class="meta-info language"  title="{{trans('documents.descriptor.language')}}">
            @if ($item->language)
				<span class="meta-label">{{trans('documents.descriptor.language')}}&nbsp;</span>{{!empty($item->language) ? trans('languages.' . $item->language) : trans('languages.no_language')}}
			@endif
		</span>
		

		<span class="meta-info creation-date" title="{{trans('documents.descriptor.added_on')}} {{$item->getCreatedAt(true)}}">
			<span class="meta-label">{{trans('documents.descriptor.added_on')}}&nbsp;</span>{{$item->getCreatedAt()}}
		</span>
		
		<span class="meta-info modified-date" title="{{trans('documents.descriptor.last_modified')}} {{$item->getUpdatedAt(true)}}">
			<span class="meta-label">{{trans('documents.descriptor.last_modified')}}&nbsp;</span>{{ $item->getUpdatedAtHumanDiff() }}
		</span>

		<span class="meta-info visibility">
			{{$item->visibility}}
		</span>

		<span class="meta-info document-type">
			{{$item->document_type}}
		</span>
		
	</div>

</div>

@elseif($item)

<div class="item" rv-on-click="select" data-inst="{{$item->getInstitutionId()}}" data-doc="{{$item->getLocalDocumentId()}}">

	<div class="icon">
		<span class="klink-document-icon klink-{{$item->documentType}}"></span>
	</div>

	<div class="badges">
		<div class="badge public" title="{{trans('documents.descriptor.is_public')}}">
			<span class="icon-social-black icon-social-black-ic_public_black_24dp"></span>
		</div>
	</div>

	<div class="thumbnail klink-{{$item->documentType}}">
		
		<img src="{{$item->thumbnailURI}}" />

	</div>

	<h2 class="title">

		@if( $item->isStarrable)
			@if( $item->isStarred )
				<a href="#star" data-action="star" class="star public active" title="{{trans('starred.remove')}}" data-id="{{$item->starId}}" data-inst="{{$item->getInstitutionId()}}" data-doc="{{$item->getLocalDocumentId()}}" data-visibility="{{$item->getVisibility()}}">

				</a>
			@else
				<a href="#star" data-action="star" class="star public" title="{{trans('starred.add')}}" data-inst="{{$item->getInstitutionId()}}" data-doc="{{$item->getLocalDocumentId()}}" data-visibility="{{$item->getVisibility()}}"></a>
			@endif
		@endif

		<a href="{{$item->documentURI}}" class="link" target="_blank" title="{{ $item->title }}">
			{{ $item->title }}
		</a>
		
	</h2>

	<div class="meta">

		<span class="meta-info institution-name">
			<span class="meta-label">{{trans('documents.descriptor.owned_by')}}&nbsp;</span>{{$item->institutionName}}
		</span>

		
		<span class="meta-info language">
            @if ($item->language)
			<?php $lang = $item->language; ?>
				<span class="meta-label">{{trans('documents.descriptor.language')}}&nbsp;</span>{{ !empty($lang) ? trans('languages.' . $lang) : trans('languages.no_language') }}
			@endif
		</span>
		

		<span class="meta-info creation-date">
			<span class="meta-label">{{trans('documents.descriptor.added_on')}}&nbsp;</span>{{$item->creationDate}}
		</span>

		<span class="meta-info visibility">
			{{$item->getVisibility()}}
		</span>

		<span class="meta-info document-type">
			{{$item->getDocumentType()}}
		</span>
		
	</div>

</div>

@endif
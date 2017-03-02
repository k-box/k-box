
<div draggable="true" class="item selectable group-item" @if(!$item->trashed()) rv-on-click="select" @endif data-id="{{$item->id}}" data-type="group" @if( isset($badge_shared) && $badge_shared ) data-shared="true" @endif data-group-id="{{$item->id}}" data-drop="true" data-drop-action="copyTo" @if(isset($share_id)) data-shareid="{{$share_id}}" @endif @if(isset($shared_with)) data-sharewith="true" @endif>




	@if(!isset($hide_checkboxes))

		<span class="selection-area"></span>

		<div class="selection">
			
			<span class="selection-tab">
				
			</span>

			<span class="select-box">
				
				<input type="checkbox" role="presentation" data-action="selectable" tabindex="-1" id="item-group-{{$item->id}}" class="checkbox">

			</span>

		</div>

	@endif

	<div class="icon" title="{{trans('groups.group_icon_label')}}">
		<span class="icon-file-black icon-file-black-ic_folder_black_24dp"></span>
	</div>

	<div class="badges">
		
		@if( isset($badge_shared) && $badge_shared )

			<div class="badge shared" title="{{trans('documents.descriptor.shared')}}">
				<span class="icon-social-black icon-social-black-ic_people_black_24dp"></span>
			</div>

		@endif
		

		@if( $item->is_private )

			<div class="badge private" title="{{trans('groups.private_badge_label')}}">
				<span class="icon-action-black icon-action-black-ic_lock_black_24dp"></span>
			</div>

		@endif

	
		

	</div>

	

	<div class="thumbnail " @if($item->color) style="background-color:#{{$item->color}} " @endif>
		
		

	</div>

	<h2 class="title">
		
		@if($item->trashed())
		<span class="link" title="{{ $item->name }}">
			{{ $item->name }}
		</span>
		@else
		<a href="{{route( isset($link_route) ? $link_route : 'documents.groups.show', $item->id)}}" class="link" title="{{ $item->name }}">
			{{ $item->name }}
		</a>
		@endif
		
	</h2>

	<div class="meta">

		@if(isset($share_id) && isset($shared_with))
			<span class="meta-info shared-with" title="{{trans('share.shared_with_label')}}">
				<span class="meta-label">{{trans('share.shared_with_label')}} </span>@include('share.with', ['with_who' => $shared_with])
			</span>			
		@endif
		
		@if(isset($share_id) && isset($shared_by))
			<span class="meta-info shared-by" title="{{trans('share.shared_by_label')}}">
				<span class="meta-label">{{trans('share.shared_by_label')}} </span>@include('share.with', ['with_who' => $shared_by])
			</span>			
		@endif

        @if($item->user)
		<span class="meta-info institution-name" title="{{trans('groups.created_by')}}">
			<span class="meta-label">{{trans('groups.created_by')}} </span>{{$item->user->name}}
		</span>
        @endif
		
		
		@if(isset($share_created_at) && isset($share_created_at_timestamp))
			<span class="meta-info modified-date" title="{{trans('share.shared_on')}} {{$share_created_at_timestamp}}">
				<span class="meta-label">{{trans('share.shared_on')}}&nbsp;</span>{{$share_created_at}}
			</span>
		@else		
		<span class="meta-info creation-date" title="{{trans('groups.created_on')}} {{$item->getCreatedAt(true)}}">
			<span class="meta-label">{{trans('groups.created_on')}} </span>{{$item->getCreatedAt()}}
		</span>
		@endif
		
	</div>


</div>
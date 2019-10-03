{{-- Collection/Group panel --}}


<div class="c-panel__header">

    <h4 class="c-panel__title">{{ $group->name }}</h4>
    
    {{-- show color and type --}}
    {{-- show if shared by you --}}

    @if( $has_share )

        <div class="item__badge" title="{{trans('documents.descriptor.shared')}}">
            @materialicon('social', 'people', 'inline-block')
        </div>

    @endif

</div>
<div class="c-panel__actions">
	<a href="{{route('documents.groups.show', $group->id)}}" class="button">{{ trans('projects.show_documents') }}</a>

	
	{{-- @if(auth()->check() && auth()->user()->id === $group->user_id )

		<a href="{{route('groups.edit', $group->id)}}" class="button">{{ trans('groups.edit_button') }}</a>
	@endif --}}
</div>

<div class="c-panel__data">

    {{-- @if($is_user_logged && $item->isMine())

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

    @endif --}}
    

	<div class="c-panel__meta">
		<span class="c-panel__label">{{trans('documents.descriptor.added_on')}}</span>{{$group->created_at->toDateString() }}
	</div>
	<div class="c-panel__meta">
        <span class="c-panel__label">{{trans('documents.descriptor.added_by')}}</span>
        @can('see_owner', $group)
            {{ optional($group->user)->name }}
        @else 
            @component('components.undisclosed_user')
        @endcan
	</div>
    

    @if( $has_share && isset($share) )

        <div class="c-panel__meta">
            <span class="c-panel__label">{{trans('share.shared_by_label')}}</span>
            {{ optional($share->user)->name }}
        </div>
        
        <div class="c-panel__meta">
            <span class="c-panel__label">{{trans('share.shared_on')}}</span>
            {{$share->created_at->toDateString()}}
        </div>
        

    @endif



</div>
@if($item instanceof KlinkDMS\Shared)

<div class="shared-list__item">

    <div class="shared-list__user-container">
        @unless($item->isPublicLink())
            @include('avatar.picture', ['user_name' => $item->sharedwith->name, 'no_link' => true, 'inline' => true])

            <div class="shared-list__who">
                <span class="shared-list__user">{{$item->sharedwith->name}}</span>
                <span class="shared-list__mail">{{$item->sharedwith->email}}</span>
            </div>
        @else 
            <div class="shared-list__who">
                <span class="shared-list__user">{{ trans('share.publiclinks.public_link') }}</span>
            </div>
        @endif
        
    </div>

    <div class="shared-list__type">
        @unless($item->isPublicLink())
        {{ trans('share.dialog.access_by_direct_share') }}
        @endif
    </div>

    <div class="shared-list__actions">
        <button data-id="{{ $item->id }}" class="js-unshare">{{ trans('share.remove') }}</button>
    </div>

</div>

@else

<div class="shared-list__item">

    <div class="shared-list__user-container">
        @include('avatar.picture', ['user_name' => $item->name, 'no_link' => true, 'inline' => true])

        <div class="shared-list__who">
            <span class="shared-list__user">{{$item->name}}</span>
            <span class="shared-list__mail">{{$item->email}}</span>
        </div>
    </div>

    <div class="shared-list__type">
        @if($item->pivot)
            <span title="{{ trans('share.dialog.access_by_project_membership_hint', ['project' => KlinkDMS\Project::find($item->pivot->project_id)->name]) }}">
            {{ trans('share.dialog.access_by_project_membership', ['project' => KlinkDMS\Project::find($item->pivot->project_id)->name]) }}
            </span>
        @elseif($item->project_id)
            <span title="{{ trans('share.dialog.access_by_project_membership_hint', ['project' => KlinkDMS\Project::find($item->project_id)->name]) }}">
            {{ trans('share.dialog.access_by_project_membership', ['project' => KlinkDMS\Project::find($item->project_id)->name]) }}
            </span>
        @endif
    </div>

    <div class="shared-list__actions">&nbsp;</div>

</div>

@endif

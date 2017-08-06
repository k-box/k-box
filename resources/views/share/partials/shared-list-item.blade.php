@if($item instanceof KlinkDMS\Shared)

<div class="shared-list__item">

    <div class="shared-list__user-container">
        @unless($item->isPublicLink())
            @component('avatar.full', [
                'image' => $item->sharedwith->avatar, 
                'name' => $item->sharedwith->name])

                {{$item->sharedwith->name}}
                <span class="shared-list__mail">{{$item->sharedwith->email}}</span>

            @endcomponent

        @else 
            <div class="shared-list__who">
                @component('avatar.full')

                    @slot('icon')
                        @materialicon('content', 'link', 'avatar__icon')
                    @endslot

                    {{trans('share.publiclinks.public_link')}}

                @endcomponent
            </div>
        @endif
        
    </div>

    <div class="shared-list__type">
        @unless($item->isPublicLink())
        {{ trans('share.dialog.access_by_direct_share') }}
        @endif
    </div>

    <div class="shared-list__actions">
        @unless($item->isPublicLink())
        <button data-id="{{ $item->id }}" class="button button--danger button--ghost js-unshare" title="{{ trans('share.remove') }}">
        @materialicon('content', 'remove_circle_outline', 'button__icon')</button>
        @endif
    </div>

</div>

@else

<div class="shared-list__item">

    <div class="shared-list__user-container">
        @component('avatar.full', [
            'image' => $item->avatar, 
            'name' => $item->name])

            {{$item->name}}
            <span class="shared-list__mail">{{$item->email}}</span>

        @endcomponent
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

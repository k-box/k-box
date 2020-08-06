@if($item instanceof KBox\Shared)

<div class="mt-1" 
    x-show="!removed"
    @form-errored="removing=false"
    @form-submitting="removing=true"
    @form-submitted="removed=true;removing=false;"
    x-data="{removing:false, removed:false}">

    <div class="flex justify-between items-center" :class="{'opacity-25': removing}">
        <div class="flex-grow">
            @component('avatar.full', [
                'image' => optional($item->sharedwith)->avatar ?? null, 
                'name' => optional($item->sharedwith)->name ?? trans('notices.disabled_user')])

                {{optional($item->sharedwith)->name}}
                <span class="text-sm text-gray-600 hidden sm:inline">{{optional($item->sharedwith)->email ?? trans('notices.disabled_user')}}</span>

            @endcomponent
        </div>

        <div class="text-sm text-gray-600 mr-2">
            {{ trans('share.dialog.access_by_direct_share') }}
        </div>

        <div class="">
            <form method="POST" 
                x-data="AsyncForm()" 
                x-on:submit.prevent="submit" 
                
                action="{{route('shares.destroy', ['share' => $item->id])}}">

                @csrf
                @method('DELETE')

                <button type="submit" class="button button--danger button--ghost" title="{{ trans('share.remove') }}">
                    @materialicon('content', 'remove_circle_outline', 'button__icon')
                </button>

                <div class="bg-red-100 text-red-800 p-1 w-full flex-shrink-0" x-show="errors" x-text="errors"></div>
            </form>
        </div>
    </div>

    

</div>

@else

<div class="flex justify-between items-center mt-1">

    <div class="flex-grow">
        @component('avatar.full', [
            'image' => $item->avatar, 
            'name' => $item->name])

            {{$item->name}}
            <span class="text-sm text-gray-600">{{$item->email}}</span>

        @endcomponent
    </div>

    <div class="">
        @if($item->pivot)
            <span class="text-sm text-gray-600" title="{{ trans('share.dialog.access_by_project_membership_hint', ['project' => KBox\Project::find($item->pivot->project_id)->name]) }}">
            {{ trans('share.dialog.access_by_project_membership', ['project' => KBox\Project::find($item->pivot->project_id)->name]) }}
            </span>
        @elseif($item->project_id)
            <span class="text-sm text-gray-600" title="{{ trans('share.dialog.access_by_project_membership_hint', ['project' => KBox\Project::find($item->project_id)->name]) }}">
            {{ trans('share.dialog.access_by_project_membership', ['project' => KBox\Project::find($item->project_id)->name]) }}
            </span>
        @endif
    </div>

</div>

@endif

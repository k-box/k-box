{{-- Collection/Group panel --}}


<div class="c-panel__header flex-col justify-start">

    <h4 class="c-panel__title">{{ $group->name }}</h4>
    
    <div class="mb-4 flex items-center {{ $is_personal ? 'text-personal-collection' : 'text-project-collection' }}">
        <span class="mr-2 rounded-full bg-white p-2 inline-flex items-center justify-center">@materialicon('action', 'label', 'inline-block fill-current')</span>
        <span class="text-white">{{trans( $is_personal ? 'groups.group_icon_label_personal' : 'groups.group_icon_label_project')}}</span>
    </div>

    @if( $has_share )

        <div class="flex items-center">
            <span class="mr-2 rounded-full bg-white p-2 inline-flex items-center justify-center">@materialicon('social', 'people', 'inline-block fill-current text-black')</span>
            <span class="text-white">{{ trans('documents.descriptor.shared') }}</span>
        </div>

    @endif

</div>
<div class="c-panel__actions">
	<a href="{{route('documents.groups.show', $group->id)}}" class="button">{{ trans('projects.show_documents') }}</a>
</div>

<div class="c-panel__data">

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
    

    @if( $has_share )

        @isset($share)    
            <div class="c-panel__meta">
                <span class="c-panel__label">{{trans('share.shared_by_label')}}</span>
                {{ optional($share->user)->name }}
            </div>

            <div class="c-panel__meta">
                <span class="c-panel__label">{{trans('share.shared_on')}}</span>
                {{$share->created_at->toDateString()}}
            </div>
        @endisset
        

    @endif



</div>
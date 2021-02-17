
<div 
    @if(isset($draggable) && $draggable) draggable="true" @endif 
    class="item @if($type==='group') group-item @else document-item @endif @if(isset($selectable) && $selectable) js-selectable item--selectable @endif" 
    @if(!($type==='group' && $trashed)) rv-on-click="select" @endif
    @if(isset($id) && $id) data-id="{{$id}}" @endif 
    @if(isset($uuid) && $uuid) data-uuid="{{$uuid}}" @endif 
    @if(isset($project) && $project) data-project="{{$project}}" @endif 
    @if($type ==='group') data-group-id="{{$id}}" @endif
    data-class="{{ $data_class }}" 
    @if($type!=='group') data-visibility="{{$visibility}}" @endif
    data-type="{{$type}}" 
    @if(isset($drop_action) && $drop_action) data-drop="true" data-drop-action="{{$drop_action}}" @endif
    @if(isset($star) && $star) data-star-id="{{$star}}" @endif 
    @if(isset($share) && $share) data-shareid="{{$share}}" @endif 
    @if( isset($shared) && $shared ) data-shared="true" @endif 
    @if(isset($shared_with) && $shared_with) data-sharewith="true" @endif>

	<div class="item__title list__column list__column--large">
	
        @if(isset($selectable) && $selectable)

            <button class="item__select js-select-button js-selection-checkbox" data-action="selectable" role="presentation" tabindex="-1" id="item-{{$data_class}}-{{$id}}">

                @materialicon('toggle', 'check_box', ' checkbox checkbox--checked')
                @materialicon('toggle', 'check_box_outline_blank', ' checkbox checkbox--unchecked')

            </button>

        @endif


        
            

        @if($type==='group')
            <div class="item__icon" title="{{trans('groups.group_icon_label')}}" @if(isset($color) && !empty($color)) style="fill:#{{$color}}" @endif>
                @materialicon('action', 'label', 'inline-block')
            </div>
        @else
            <div class="item__icon" title="{{$type}}">
                @materialicon('action', 'description', 'inline-block')
            </div>
        @endif
            

	

        @if($type!=='group' && isset($thumbnail) && is_string($thumbnail))
        <div class="item__thumbnail klink-{{$type}}">
            
            <img src="{{asset('images/unknown.png')}}" data-src="{{ $thumbnail }}" />

        </div>
        @elseif($type==='group')
        <div class="item__thumbnail klink-project" style="@if($color) background-color: #{{$color}} @endif">

        </div>
        @endif


        <a href="{{ $url }}" class="item__link @if($type==='group') js-tree-item-inner @endif" rel="noopener" target="_blank" title="{{ $name }}">
            {{ $name }}
        </a>
        
        <div class="item__badges">
        @if(isset($starrable) && $starrable && isset($local_document_id) && (!isset($context) || (isset($context) && $context!=='trash')))

            <x-star-button :starID="$star" :documentID="$local_document_id" class="item__star" />
            
            @if(isset($trashed) && $trashed)

                <div class="item__badge" title="{{ trans('starred.starred_in_trash') }}">
                    @materialicon('action', 'delete', 'inline-block')
                </div>

            @endif

        @endif



            @if( isset($is_public) && $is_public )
            
                <div class="item__badge" title="{{trans( isset($is_published) && $is_published ? 'documents.descriptor.is_public' : 'share.dialog.publishing')}}">
                    @materialicon('social', 'public', 'inline-block')
                </div>
            
            @else

                <div class="item__badge" title="{{trans('documents.descriptor.private')}}">
                    @materialicon('action', 'lock', 'inline-block')
                </div>

            @endif

            @if( isset($shared) && $shared )

                <div class="item__badge" title="{{trans('documents.descriptor.shared')}}">
                    @materialicon('social', 'people', 'inline-block')
                </div>

            @endif
            
            @if( isset($has_duplicates) && $has_duplicates )

                <div class="item__badge" title="{{trans('documents.duplicates.badge')}}">
                    @materialicon('content', 'content_copy', 'inline-block')
                </div>

            @endif
        
        </div>

	</div>
	

    @if(!isset($share) || isset($share) && !$share)
		<span class="item__detail list__column list__column--fixed list__column--hideable" title="{{trans('documents.descriptor.added_by')}} {{$added_by}}">

            @if($type !=='group' && isset($instance))
            
                @can('see_owner', $instance)
                
                    {{$added_by}}

                @else 
                    @component('components.undisclosed_user')
                        
                    @endcomponent
                @endcan
            @else 
                {{$added_by}}
            @endif
		</span>
    @endif
		
    @if(isset($share) && isset($shared_by) && $shared_by !== false)
        <span class="item__detail list__column list__column--fixed shared-by" title="{{trans('share.shared_by_label')}}">
            @include('share.with', ['with_who' => $shared_by])
        </span>			
    @endif
		
		
    @if(isset($shared_on) &&  $shared_on)
        <span class="item__detail list__column modified-date" title="{{trans('share.shared_on')}} @datetime($shared_on)">
            @date($shared_on)
        </span>
    @endif
		
    @unless(isset($shared_on) &&  $shared_on)
        <span class="item__detail list__column modified-date" title="{{trans('documents.descriptor.last_modified')}} @datetime($modified_at)">
            {{ $modified_at_diff ?? optional($modified_at)->render() }}
        </span>
    @endif
    
    <span class="item__detail list__column list__column--hideable language"  title="{{trans('documents.descriptor.language')}} {{isset($language) && !empty($language) ? trans('languages.' . $language) : '-'}}">
        {{isset($language) && !empty($language) ? trans('languages.' . $language) : '-'}}
    </span>
	
</div>
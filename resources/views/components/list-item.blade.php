
<div 
    @if(isset($draggable) && $draggable) draggable="true" @endif 
    class="item @if($type==='group') group-item @else document-item @endif @if(isset($selectable) && $selectable) js-selectable item--selectable @endif" 
    @if(!($type==='group' && $trashed)) rv-on-click="select" @endif
    @if(isset($id) && $id) data-id="{{$id}}" @endif 
    @if(isset($project) && $project) data-project="{{$project}}" @endif 
    @if($type ==='group') data-group-id="{{$id}}" @endif
    data-class="{{ $data_class }}" 
    @if($type!=='group') data-institution="{{$institution}}" @endif
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

                @materialicon('toggle', 'check_box', 'checkbox checkbox--checked')
                @materialicon('toggle', 'check_box_outline_blank', 'checkbox checkbox--unchecked')

            </button>

        @endif


        
            

        @if($type==='group')
            <div class="item__icon" title="{{trans('groups.group_icon_label')}}" @if(isset($color) && !empty($color)) style="fill:#{{$color}}" @endif>
                @materialicon('action', 'label')
            </div>
        @else
            <div class="item__icon" title="{{$type}}">
                @materialicon('action', 'description')
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


        <a href="{{ $url }}" class="item__link" rel="noopener" target="_blank" title="{{ $name }}">
            {{ $name }}
        </a>
        
        <div class="item__badges">
        
        @if(isset($starrable) && $starrable && (!isset($trashed) || (isset($trashed) && !$trashed)))

            <button data-action="star" 
                class="item__star @if($is_public) item__star--public @endif @if( $starred ) item__star--starred @endif"
                @if($star!== false) data-id="{{$star}}" @endif
                data-inst="{{$institution_klink_id}}" 
                data-doc="{{$local_document_id}}" 
                data-visibility="{{$visibility}}">
                @materialicon('toggle', 'star', 'star star--starred', ['alt' => trans('starred.remove')])
                @materialicon('toggle', 'star_border', 'star star--not-starred', ['alt' => trans('starred.add')])
            </button>

        @endif



            @if( isset($visibility) && $visibility === 'public' )
            
                <div class="item__badge" title="{{trans('documents.descriptor.is_public')}}">
                    @materialicon('social', 'public')
                </div>
            
            @elseif( isset($visibility) && $visibility === 'private' )

                <div class="item__badge" title="{{trans('documents.descriptor.private')}}">
                    @materialicon('action', 'lock')
                </div>

            @endif

            @if( isset($shared) && $shared )

                <div class="item__badge" title="{{trans('documents.descriptor.shared')}}">
                    @materialicon('social', 'people')
                </div>

            @endif

            {{-- @if( isset($badge_error) && $badge_error ) --}}

                {{-- <div class="item__badge item__badge--error" title="{{trans('documents.descriptor.indexing_error')}}">
                    @materialicon('action', 'report_problem')
                </div> --}}

            {{-- @endif --}}
        
        </div>

	</div>
	

    @if(!isset($share) || isset($share) && !$share)
		<span class="item__detail list__column list__column--hideable" title="{{trans('documents.descriptor.added_by')}} {{$added_by}}">
            {{$added_by}}
		</span>
    @endif
		
    @if(isset($share) && isset($shared_by) && $shared_by !== false)
        <span class="item__detail list__column shared-by" title="{{trans('share.shared_by_label')}}">
            @include('share.with', ['with_who' => $shared_by])
        </span>			
    @endif
		
		
    @if(isset($shared_on) && isset($shared_on_diff) && $shared_on && $shared_on_diff)
        <span class="item__detail list__column modified-date" title="{{trans('share.shared_on')}} {{$shared_on}}">
            {{$shared_on_diff}}
        </span>
    {{-- @else
        <span class="item__detail list__column item__detail--creation-date" title="{{trans('documents.descriptor.added_on')}} {{$created_at}}">
            {{$created_at_diff}}
        </span> --}}
    @endif
		
    @unless(isset($shared_on) && isset($shared_on_diff) && $shared_on && $shared_on_diff)
        <span class="item__detail list__column modified-date" title="{{trans('documents.descriptor.last_modified')}} {{$modified_at}}">
            {{ $modified_at_diff or $modified_at }}
        </span>
    @endif
    
    <span class="item__detail list__column list__column--hideable language"  title="{{trans('documents.descriptor.language')}} {{isset($language) && !empty($language) ? trans('languages.' . $language) : '-'}}">
        {{isset($language) && !empty($language) ? trans('languages.' . $language) : '-'}}
    </span>
	
</div>
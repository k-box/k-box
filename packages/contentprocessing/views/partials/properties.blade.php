<div class="file-properties">


    @stack('properties-before')
        
    {{-- <div class="file-properties__section">
        <span class="file-properties__section-label">{{ trans('preview::properties.sections.general') }}</span>
        <span class="file-properties__section-hint">{{ trans('preview::properties.sections.general_hint') }}</span>
    </div> --}}

    


    {{-- <div class="file-properties__property">
        <span class="file-properties__label">{{trans('preview::properties.type')}}</span>
        <span class="file-properties__value">{{ isset($type) ? $type : '' }}</span>
    </div>
    
    <div class="file-properties__property">
        <span class="file-properties__label">{{trans('preview::properties.size')}}</span>
        <span class="file-properties__value">{{ isset($size) ? $size : '' }}</span>
    </div> --}}
    
    @if(isset($properties))

        <div class="file-properties__section">
            <span class="file-properties__section-label">{{ trans('preview::properties.sections.document') }}</span>
            <span class="file-properties__section-hint">{{ trans('preview::properties.sections.document_hint') }}</span>
        </div>

        <div class="file-properties__property">
            <span class="file-properties__label">{{trans('preview::properties.title')}}</span>
            <span class="file-properties__value">{{ isset($properties) ? $properties->title() : '' }}</span>
        </div>

        <div class="file-properties__property">
            <span class="file-properties__label">{{trans('preview::properties.creator')}}</span>
            <span class="file-properties__value">{{ isset($properties) ? $properties->creator() : '' }}</span>
        </div>

        <div class="file-properties__property">
            <span class="file-properties__label">{{trans('preview::properties.description')}}</span>
            <span class="file-properties__value">{{ isset($properties) ? $properties->description() : '' }}</span>
        </div>

        <div class="file-properties__property">
            <span class="file-properties__label">{{trans('preview::properties.subject')}}</span>
            <span class="file-properties__value">{{ isset($properties) ? $properties->subject() : '' }}</span>
        </div>

        <div class="file-properties__property">
            <span class="file-properties__label">{{trans('preview::properties.createdAt')}}</span>
            <span class="file-properties__value">{{ isset($properties) ? $properties->createdAt() : '' }}</span>
        </div>

        <div class="file-properties__property">
            <span class="file-properties__label">{{trans('preview::properties.modifiedAt')}}</span>
            <span class="file-properties__value">{{ isset($properties) ? $properties->modifiedAt() : '' }}</span>
        </div>

        <div class="file-properties__property">
            <span class="file-properties__label">{{trans('preview::properties.lastModifiedBy')}}</span>
            <span class="file-properties__value">{{ isset($properties) ? $properties->lastModifiedBy() : '' }}</span>
        </div>

        <div class="file-properties__property">
            <span class="file-properties__label">{{trans('preview::properties.keywords')}}</span>
            <span class="file-properties__value">{{ isset($properties) ? $properties->keywords() : '' }}</span>
        </div>

        <div class="file-properties__property">
            <span class="file-properties__label">{{trans('preview::properties.category')}}</span>
            <span class="file-properties__value">{{ isset($properties) ? $properties->category() : '' }}</span>
        </div>

    @endif

    @yield('properties-after')

</div>
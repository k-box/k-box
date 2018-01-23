<div class="preview js-preview">
    
    <div class="preview__header">

        <div class="preview__title-container">
        
            @if(!isset($is_full_page))
            <button class="preview__button js-preview-back-button">{{ trans('preview::actions.back') }}</button>
            @endif

            @if(isset($documentTitle))
            <span class="preview__title">{{ $documentTitle }}</span>
            @endif

            @yield('title-actions')
        
        </div>



        <div class="preview__actions">

            @yield('actions')
        
            <button class="preview__button js-preview-download-button">{{ trans('preview::actions.download') }}</button>
            
            @if(!isset($is_full_page))
            <button class="preview__button js-preview-open-button">{{ trans('preview::actions.open_new_window') }}</button>
            @endif
            
            <button class="preview__button preview__button--expandable js-preview-details-button">{{ trans('preview::actions.details') }}<span class="preview__button-close">{{ trans('preview::actions.close') }}</span></button>

        </div>
    
    </div>


    <div class="preview__body">

        <div class="preview__area js-preview-area">
            {{-- <div class="preview__navigation">
            
                navigation inside the preview
            
            </div> --}}

            <div class="preview__content js-preview-content">
                @if(isset($render))
                    {!! $render !!}
                @endif
            </div>
        </div>
    
        <div class="preview__sidebar js-preview-sidebar">
        
            @include('preview::partials.properties')
        
        </div>
    </div>


</div>
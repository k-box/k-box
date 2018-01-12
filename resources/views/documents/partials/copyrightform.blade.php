{{--  form to manage license and copyright information  --}}
    
    <div class="c-form__field c-section--top-separated">
        <label for="copyright_usage">{{trans('documents.edit.license')}}</label>
        <p class="description">{{ trans('documents.edit.license_help') }}</p>
        @if( $errors->has('copyright_usage') )
            <span class="field-error">{{ implode(",", $errors->get('copyright_usage'))  }}</span>
        @endif

        <select class="c-form__input c-form__input--larger" name="copyright_usage" id="copyright_usage" @if(!$document->isMine() || !$can_edit_document) disabled @endif>
            
            <option disabled selected value="">{{ trans('administration.documentlicenses.default.select') }}</option>                    
            
            @forelse($available_licenses as $license)
            <option value="{{ $license->id }}" @if(old('copyright_usage', $selected_license ? $selected_license->id : '') === $license->id) selected  @endif>{{ $license->title }}</option>
            @empty
            
            @endforelse
        </select>
        
        <a href="https://creativecommons.org/choose/" target="_blank" rel="noopener nofollow">{{ trans('documents.edit.license_choose_help_button') }}</a>
        
    </div>

    <div class="c-form__field c-section--top-separated">
        <label for="copyright_usage">{{trans('documents.edit.copyright_owner')}}</label>
        <p class="description">{{ trans('documents.edit.copyright_owner_help') }}</p>
        
        <label for="copyright_owner_name">{{ trans('documents.edit.copyright_owner_name_label') }}</label>
        @if( $errors->has('copyright_owner_name') )
        <span class="field-error">{{ implode(",", $errors->get('copyright_owner_name'))  }}</span>
        @endif
        <input type="text" name="copyright_owner_name" id="copyright_owner_name" value="{{old('copyright_owner_name', isset($owner) ? $owner->get('name') : '')}}" class="c-form__input c-form__input--larger" @if(!$document->isMine() || !$can_edit_document) disabled @endif> 
        
        <label for="copyright_owner_email">{{ trans('documents.edit.copyright_owner_email_label') }}</label>
        @if( $errors->has('copyright_owner_email') )
        <span class="field-error">{{ implode(",", $errors->get('copyright_owner_email'))  }}</span>
        @endif
        <input type="text" name="copyright_owner_email" id="copyright_owner_email" value="{{old('copyright_owner_email', isset($owner) ? $owner->get('email') : '')}}" class="c-form__input c-form__input--larger" @if(!$document->isMine() || !$can_edit_document) disabled @endif> 
        
        <label for="copyright_owner_website">{{ trans('documents.edit.copyright_owner_website_label') }}</label>
        @if( $errors->has('copyright_owner_website') )
            <span class="field-error">{{ implode(",", $errors->get('copyright_owner_website'))  }}</span>
            @endif
            <input type="text" name="copyright_owner_website" id="copyright_owner_website" placeholder="https://" value="{{old('copyright_owner_website', isset($owner) ? $owner->get('website') : '')}}" class="c-form__input c-form__input--larger" @if(!$document->isMine() || !$can_edit_document) disabled @endif> 
            
        <label for="copyright_owner_address">{{ trans('documents.edit.copyright_owner_address_label') }}</label>
        @if( $errors->has('copyright_owner_address') )
            <span class="field-error">{{ implode(",", $errors->get('copyright_owner_address'))  }}</span>
            @endif
            <input type="text" name="copyright_owner_address" id="copyright_owner_address" value="{{old('copyright_owner_address', isset($owner) ? $owner->get('address') : '')}}" class="c-form__input c-form__input--larger" @if(!$document->isMine() || !$can_edit_document) disabled @endif> 
            
        </div>
    

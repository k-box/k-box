
    @include('errors.list')


    {{ csrf_field() }}

    <div class="c-form__field">
        
        <label>{{trans('administration.institutions.labels.klink_id')}}</label>
        @if( $errors->has('klink_id') )
            <span class="field-error">{{ implode(",", $errors->get('klink_id'))  }}</span>
        @endif
        
        @if(!isset($can_change_klink_id) || (isset($can_change_klink_id) && !$can_change_klink_id))
        
        <input type="hidden" required name="klink_id" value="{{old('klink_id', isset($institution) ? $institution->klink_id : '')}}" />
        <strong>{{old('klink_id', isset($institution) ? $institution->klink_id : '')}}</strong>
        @else
        
            <input class="c-form__input" type="text" required name="klink_id" value="{{old('klink_id', isset($institution) ? $institution->klink_id : '')}}" />
        
        @endif
    </div>

    <div  class="c-form__field">
        
        <label>{{trans('administration.institutions.labels.name')}}</label>
        @if( $errors->has('name') )
            <span class="field-error">{{ implode(",", $errors->get('name'))  }}</span>
        @endif
        <input class="c-form__input c-form__input--larger" type="text" required name="name" value="{{old('name', isset($institution) ? $institution->name : '')}}" />
    </div>
    
    <div  class="c-form__field">
        
        <label>{{trans('administration.institutions.labels.email')}}</label>
        @if( $errors->has('email') )
            <span class="field-error">{{ implode(",", $errors->get('email'))  }}</span>
        @endif
        <input class="c-form__input" type="email" required name="email" value="{{old('email', isset($institution) ? $institution->email : '')}}" />
    </div>
    
    <div  class="c-form__field">
        
        <label>{{trans('administration.institutions.labels.phone')}}</label>
        @if( $errors->has('phone') )
            <span class="field-error">{{ implode(",", $errors->get('phone'))  }}</span>
        @endif
        <input class="c-form__input" type="text" required name="phone" value="{{old('phone', isset($institution) ? $institution->phone : '')}}" />
    </div>
    
    <div  class="c-form__field">
        
        <label>{{trans('administration.institutions.labels.url')}}</label>
        @if( $errors->has('url') )
            <span class="field-error">{{ implode(",", $errors->get('url'))  }}</span>
        @endif
        <input class="c-form__input" type="text" required name="url"  placeholder="http://" value="{{old('url', isset($institution) ? $institution->url : '')}}" />
    </div>
    
    <div  class="c-form__field">
        
        <label>{{trans('administration.institutions.labels.thumbnail_url')}}</label>
        @if( $errors->has('thumbnail_uri') )
            <span class="field-error">{{ implode(",", $errors->get('thumbnail_uri'))  }}</span>
        @endif
        <input class="c-form__input" type="text" required name="thumbnail_uri" placeholder="http://" value="{{old('thumbnail_uri', isset($institution) ? $institution->thumbnail_uri : '')}}" />
    </div>
    
    <div  class="c-form__field">
        
        <label>{{trans('administration.institutions.labels.address_street')}}</label>
        @if( $errors->has('address_street') )
            <span class="field-error">{{ implode(",", $errors->get('address_street'))  }}</span>
        @endif
        <input class="c-form__input" type="text" name="address_street" value="{{old('address_street', isset($institution) ? $institution->address_street : '')}}" />
    </div>
    
    <div  class="c-form__field">
        
        <label>{{trans('administration.institutions.labels.address_locality')}}</label>
        @if( $errors->has('address_locality') )
            <span class="field-error">{{ implode(",", $errors->get('address_locality'))  }}</span>
        @endif
        <input class="c-form__input" type="text" name="address_locality" value="{{old('address_locality', isset($institution) ? $institution->address_locality : '')}}" />
    </div>
    
    <div  class="c-form__field">
        
        <label>{{trans('administration.institutions.labels.address_country')}}</label>
        @if( $errors->has('address_country') )
            <span class="field-error">{{ implode(",", $errors->get('address_country'))  }}</span>
        @endif
        <input class="c-form__input" type="text" name="address_country" value="{{old('address_country', isset($institution) ? $institution->address_country : '')}}" />
    </div>
    
    <div  class="c-form__field">
        
        <label>{{trans('administration.institutions.labels.address_zip')}}</label>
        @if( $errors->has('address_zip') )
            <span class="field-error">{{ implode(",", $errors->get('address_zip'))  }}</span>
        @endif
        <input class="c-form__input" type="text" name="address_zip" value="{{old('address_zip', isset($institution) ? $institution->address_zip : '')}}" />
    </div>


    <div  class="c-form__buttons">
        
        <button type="submit" class="button">{{$submit_text}}</button> {{trans('actions.or_alt')}} <a href="{{route('administration.institutions.index')}}">{{trans('actions.cancel')}}</a>
    </div>

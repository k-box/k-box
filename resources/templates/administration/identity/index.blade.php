@extends('management-layout')

@section('sub-header')
        
    <a href="{{route('administration.index')}}" class="parent">{{trans('administration.page_title')}}</a> {{$pagetitle}}

@stop

@section('action-menu')


{{-- <div class="action-group">
    <a href="{{ route('administration.institutions.create') }}" class="button">
        <span class="btn-icon icon-content-white icon-content-white-ic_add_circle_outline_white_24dp"></span>{{trans('administration.institutions.create_institutions_btn')}}
    </a>
    
</div> --}}


@stop

@section('content')

<div class="row">

    <div class="two columns">

        @include('administration.adminmenu', ['small' => true, 'compact' => true])

    </div>

    <div class="ten columns c-page">
        
        @include('errors.list')

     
        @unless($is_configured)
            <div class="c-message">
                {{trans('administration.identity.not_complete')}}

                @if(!empty($contacts))
                    {{trans('administration.identity.suggestion_based_on_institution_hint')}}
                @endif
            </div>
        @endif

        <form  method="post" class="c-form" action="{{route('administration.identity.store')}}">
        
            <input type="hidden" name="_token" value="{{{ csrf_token() }}}"> 


            <div class="c-section">
                <h4 class="c-section__title">{{trans('administration.identity.page_title')}}</h4>
                <p class="c-section__description">{{trans('administration.identity.description')}}</p>

                <div class="c-form__field">
                    
                    <label>{{trans('administration.institutions.labels.name')}}</label>
                    @if( $errors->has('name') )
                        <span class="field-error">{{ implode(",", $errors->get('name'))  }}</span>
                    @endif
                    <input type="text" required name="name" value="{{old('name', isset($contacts['name']) && !is_null($contacts['name']) ? $contacts['name'] : '')}}" />
                </div>
                
                <div class="c-form__field">
                    
                    <label>{{trans('administration.institutions.labels.email')}}</label>
                    @if( $errors->has('email') )
                        <span class="field-error">{{ implode(",", $errors->get('email'))  }}</span>
                    @endif
                    <input type="email" required name="email" value="{{old('email', isset($contacts['email']) && !is_null($contacts['email']) ? $contacts['email'] : '')}}" />
                </div>
                
                <div class="c-form__field">
                    
                    <label>{{trans('administration.institutions.labels.phone')}}</label>
                    @if( $errors->has('phone') )
                        <span class="field-error">{{ implode(",", $errors->get('phone'))  }}</span>
                    @endif
                    <input type="text" name="phone" value="{{old('phone', isset($contacts['phone']) && !is_null($contacts['phone']) ? $contacts['phone'] : '')}}" />
                </div>
                
                <div class="c-form__field">
                    
                    <label>{{trans('administration.institutions.labels.url')}}</label>
                    @if( $errors->has('website') )
                        <span class="field-error">{{ implode(",", $errors->get('website'))  }}</span>
                    @endif
                    <input type="text" name="website"  placeholder="http://" value="{{old('website', isset($contacts['website']) && !is_null($contacts['website']) ? $contacts['website'] : '')}}" />
                </div>
                
                
                <div class="c-form__field">
                    
                    <label>{{trans('administration.institutions.labels.thumbnail_url')}}</label>
                    @if( $errors->has('image') )
                        <span class="field-error">{{ implode(",", $errors->get('image'))  }}</span>
                    @endif
                    <input type="text" name="image" placeholder="http://" value="{{old('image', isset($contacts['image']) && !is_null($contacts['image']) ? $contacts['image'] : '')}}" />
                </div>
                
                <div class="c-form__field">
                    
                    <label>{{trans('administration.institutions.labels.address_street')}}</label>
                    @if( $errors->has('address_street') )
                        <span class="field-error">{{ implode(",", $errors->get('address_street'))  }}</span>
                    @endif
                    <input type="text" name="address_street" value="{{old('address_street', isset($contacts['address_street']) && !is_null($contacts['address_street']) ? $contacts['address_street'] : '')}}" />
                </div>
                
                <div class="c-form__field">
                    
                    <label>{{trans('administration.institutions.labels.address_locality')}}</label>
                    @if( $errors->has('address_locality') )
                        <span class="field-error">{{ implode(",", $errors->get('address_locality'))  }}</span>
                    @endif
                    <input type="text" name="address_locality" value="{{old('address_locality', isset($contacts['address_locality']) && !is_null($contacts['address_locality']) ? $contacts['address_locality'] : '')}}" />
                </div>
                
                <div class="c-form__field">
                    
                    <label>{{trans('administration.institutions.labels.address_country')}}</label>
                    @if( $errors->has('address_country') )
                        <span class="field-error">{{ implode(",", $errors->get('address_country'))  }}</span>
                    @endif
                    <input type="text" name="address_country" value="{{old('address_country', isset($contacts['address_country']) && !is_null($contacts['address_country']) ? $contacts['address_country'] : '')}}" />
                </div>
                
                <div class="c-form__field">
                    
                    <label>{{trans('administration.institutions.labels.address_zip')}}</label>
                    @if( $errors->has('address_zip') )
                        <span class="field-error">{{ implode(",", $errors->get('address_zip'))  }}</span>
                    @endif
                    <input type="text" name="address_zip" value="{{old('address_zip', isset($contacts['address_zip']) && !is_null($contacts['address_zip']) ? $contacts['address_zip'] : '')}}" />
                </div>


            </div>
            <div class="c-form__buttons">
                
                <button class="button button-primary" type="submit">{{trans('administration.settings.save_btn')}}</button> {{trans('actions.or_alt')}} <a href="{{route('administration.index')}}">{{trans('actions.cancel')}}</a>
            </div>


            
            
        </form>


    </div>

</div>

@stop
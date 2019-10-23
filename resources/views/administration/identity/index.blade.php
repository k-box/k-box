@extends('administration.layout')

@section('breadcrumbs')
        
    <a href="{{route('administration.index')}}"  class="breadcrumb__item">{{trans('administration.page_title')}}</a> <span class="breadcrumb__item--current">{{$pagetitle}}</span>

@stop

@section('action-menu')


@stop

@section('page')
        
        @include('errors.list')

     
        @unless($is_configured)
            <div class="c-message">
                {{trans('administration.identity.not_complete')}}
            </div>
        @endif

        <form  method="post" class="" action="{{route('administration.identity.store')}}">
        
            {{ csrf_field() }}


            <div class=" ">
                <h4 class="my-4">{{trans('administration.identity.page_title')}}</h4>
                <p class="form-description">{{trans('administration.identity.description')}}</p>

                <div class=" mb-4">
                    
                    <label>{{trans('administration.institutions.labels.name')}}</label>
                    @if( $errors->has('name') )
                        <span class="field-error">{{ implode(",", $errors->get('name'))  }}</span>
                    @endif
                    <input class="form-input block w-2/3" type="text" required name="name" value="{{old('name', isset($contacts['name']) && !is_null($contacts['name']) ? $contacts['name'] : '')}}" />
                </div>
                
                <div class=" mb-4">
                    
                    <label>{{trans('administration.institutions.labels.email')}}</label>
                    @if( $errors->has('email') )
                        <span class="field-error">{{ implode(",", $errors->get('email'))  }}</span>
                    @endif
                    <input class="form-input block w-2/3" type="email" required name="email" value="{{old('email', isset($contacts['email']) && !is_null($contacts['email']) ? $contacts['email'] : '')}}" />
                </div>
                
                <div class=" mb-4">
                    
                    <label>{{trans('administration.institutions.labels.phone')}}</label>
                    @if( $errors->has('phone') )
                        <span class="field-error">{{ implode(",", $errors->get('phone'))  }}</span>
                    @endif
                    <input class="form-input block" type="text" name="phone" value="{{old('phone', isset($contacts['phone']) && !is_null($contacts['phone']) ? $contacts['phone'] : '')}}" />
                </div>
                
                <div class=" mb-4">
                    
                    <label>{{trans('administration.institutions.labels.url')}}</label>
                    @if( $errors->has('website') )
                        <span class="field-error">{{ implode(",", $errors->get('website'))  }}</span>
                    @endif
                    <input class="form-input block w-2/3" type="text" name="website"  placeholder="http://" value="{{old('website', isset($contacts['website']) && !is_null($contacts['website']) ? $contacts['website'] : '')}}" />
                </div>
                
                
                <div class=" mb-4">
                    
                    <label>{{trans('administration.institutions.labels.thumbnail_url')}}</label>
                    @if( $errors->has('image') )
                        <span class="field-error">{{ implode(",", $errors->get('image'))  }}</span>
                    @endif
                    <input class="form-input block w-2/3" type="text" name="image" placeholder="http://" value="{{old('image', isset($contacts['image']) && !is_null($contacts['image']) ? $contacts['image'] : '')}}" />
                </div>
                
                <div class=" mb-4">
                    
                    <label>{{trans('administration.institutions.labels.address_street')}}</label>
                    @if( $errors->has('address_street') )
                        <span class="field-error">{{ implode(",", $errors->get('address_street'))  }}</span>
                    @endif
                    <input class="form-input block w-2/3" type="text" name="address_street" value="{{old('address_street', isset($contacts['address_street']) && !is_null($contacts['address_street']) ? $contacts['address_street'] : '')}}" />
                </div>
                
                <div class=" mb-4">
                    
                    <label>{{trans('administration.institutions.labels.address_locality')}}</label>
                    @if( $errors->has('address_locality') )
                        <span class="field-error">{{ implode(",", $errors->get('address_locality'))  }}</span>
                    @endif
                    <input class="form-input block w-2/3" type="text" name="address_locality" value="{{old('address_locality', isset($contacts['address_locality']) && !is_null($contacts['address_locality']) ? $contacts['address_locality'] : '')}}" />
                </div>
                
                <div class=" mb-4">
                    
                    <label>{{trans('administration.institutions.labels.address_country')}}</label>
                    @if( $errors->has('address_country') )
                        <span class="field-error">{{ implode(",", $errors->get('address_country'))  }}</span>
                    @endif
                    <input class="form-input block" type="text" name="address_country" value="{{old('address_country', isset($contacts['address_country']) && !is_null($contacts['address_country']) ? $contacts['address_country'] : '')}}" />
                </div>
                
                <div class=" mb-4">
                    
                    <label>{{trans('administration.institutions.labels.address_zip')}}</label>
                    @if( $errors->has('address_zip') )
                        <span class="field-error">{{ implode(",", $errors->get('address_zip'))  }}</span>
                    @endif
                    <input class="form-input block" type="text" name="address_zip" value="{{old('address_zip', isset($contacts['address_zip']) && !is_null($contacts['address_zip']) ? $contacts['address_zip'] : '')}}" />
                </div>


            </div>
            <div class="c-form__buttons">
                
                <button class="button button-primary" type="submit">{{trans('administration.settings.save_btn')}}</button> {{trans('actions.or_alt')}} <a href="{{route('administration.index')}}">{{trans('actions.cancel')}}</a>
            </div>


            
            
        </form>



@stop
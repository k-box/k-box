@extends('administration.layout')

@section('breadcrumbs')
        
    <a href="{{route('administration.index')}}"  class="breadcrumb__item">{{trans('administration.page_title')}}</a> <span class="breadcrumb__item--current">{{$pagetitle}}</span>

@stop

@section('action-menu')




@stop

@section('page')
        
        @include('errors.list')

            <form  method="post" class="" action="{{route('administration.settings.store')}}">
    
                {{ csrf_field() }}
                
                
                <div class=" ">
                    <h4 class="my-4">{{trans('networks.settings.section')}}</h4>
                    <p class="form-description">{{trans('networks.settings.section_help')}}</p>
                
            
                    <div>                
                        <input type="checkbox" name="public_core_enabled" id="public_core_enabled" value="true" @if(isset($public_core_enabled) && $public_core_enabled) checked @endif /><label for="public_core_enabled">{{trans('networks.settings.enabled')}}</label>
                    </div>

                    <div class=" mb-4">
                        
                        <div class=" mb-4">
                        <label for="public_core_url">{{trans('networks.settings.url')}}</label>
                        @if( isset($errors) && $errors->has('public_core_url') )
                            <span class="field-error">{{ implode(",", $errors->get('public_core_url'))  }}</span>
                        @endif
                        <input class="form-input block w-2/3" type="text" required name="public_core_url" id="public_core_url" value="{{old('public_core_url', isset($public_core_url) ? $public_core_url : '')}}">
                        </div>

                        <div class=" mb-4">
                        <label for="public_core_password">{{trans('networks.settings.password')}}</label>
                        @if( isset($errors) && $errors->has('public_core_password') )
                            <span class="field-error">{{ implode(",", $errors->get('public_core_password'))  }}</span>
                        @endif
                        <input class="form-input block" type="password" required name="public_core_password" id="public_core_password" value="{{old('public_core_password', isset($public_core_password) ? $public_core_password : '')}}">
                        </div>
                    </div>
                    
                    <div class=" mb-4">

                        <strong>{{ trans('networks.settings.name_section') }}</strong><br/>
                        <p class="form-description">{{ trans('networks.settings.name_section_help') }}</span>

                        <div class=" mb-4">
                        <label for="public_core_network_name_en">{{trans('networks.settings.name_en')}}</label>
                        @if( isset($errors) && $errors->has('public_core_network_name_en') )
                            <span class="field-error">{{ implode(",", $errors->get('public_core_network_name_en'))  }}</span>
                        @endif
                        <input class="form-input block" type="text" name="public_core_network_name_en" id="public_core_network_name_en" value="{{old('public_core_network_name_en', isset($public_core_network_name_en) ? $public_core_network_name_en : '')}}">
                        </div>

                        <div class=" mb-4">
                        <label for="public_core_network_name_ru">{{trans('networks.settings.name_ru')}}</label>
                        @if( isset($errors) && $errors->has('public_core_network_name_ru') )
                            <span class="field-error">{{ implode(",", $errors->get('public_core_network_name_ru'))  }}</span>
                        @endif
                        <input class="form-input block" type="text" name="public_core_network_name_ru" id="public_core_network_name_ru" value="{{old('public_core_network_name_ru', isset($public_core_network_name_ru) ? $public_core_network_name_ru : '')}}">
                        </div>


                    </div>
                    
                    <div class=" mb-4">

                        <strong>{{ trans('networks.settings.streaming_section') }}</strong><br/>
                        <p class="form-description">{{ trans('networks.settings.streaming_section_help') }}</span>

                        <div class=" mb-4">
                        <label for="streaming_service_url">{{trans('networks.settings.streaming_service_url')}}</label>
                        @if( isset($errors) && $errors->has('streaming_service_url') )
                            <span class="field-error">{{ implode(",", $errors->get('streaming_service_url'))  }}</span>
                        @endif
                        <input class="form-input block w-2/3" type="text" name="streaming_service_url" id="streaming_service_url" value="{{old('streaming_service_url', isset($streaming_service_url) ? $streaming_service_url : '')}}">
                        </div>

                    </div>
                    
                    <div class="c-form__buttons">
                        <button type="submit" class="button" id="public-settings-save-btn" name="public-settings-save-btn">
                            {{trans('administration.settings.save_btn')}}
                        </button>
                    </div>

                </div>
            
            </form>
        

@stop
@extends('administration.layout')

@section('breadcrumbs')
        
    <a href="{{route('administration.index')}}"  class="breadcrumb__item">{{trans('administration.page_title')}}</a> <span class="breadcrumb__item--current">{{$pagetitle}}</span>

@stop

@section('action-menu')




@stop

@section('page')
        
        @include('errors.list')
        
        

            <form  method="post" class="c-form" action="{{route('administration.settings.store')}}">
    
                @csrf

                <div class="c-section c-section--separated">
                    <h4 class="c-section__title">{{trans('administration.settings.support_section')}}</h4>
                    <p class="c-section__description">{{trans('administration.settings.support_section_help')}}</p>
            
                    <div class="c-form__field">
                        <label for="support_token">{{trans('administration.settings.support_token_field')}}</label>
                        @if( isset($errors) && $errors->has('support_token') )
                            <span class="field-error">{{ implode(",", $errors->get('support_token'))  }}</span>
                        @endif
                        <input class="c-form__input c-form__input--larger" type="text" name="support_token" id="support_token" value="{{old('support_token', isset($support_token) ? $support_token : '')}}">
                    </div>
                    

                    <div class="c-form__buttons">

                        <button type="submit" class="button" id="support-settings-save-btn" name="support-settings-save-btn">
                            {{trans('administration.settings.support_save_btn')}}
                        </button>
                    </div>

                </div>
            
            </form>
        
        
        

            <form  method="post" class="c-form" action="{{route('administration.settings.store')}}">
    
                @csrf
                
                
                <div class="c-section c-section--separated">
                    <h4 class="c-section__title">{{trans('networks.settings.section')}}</h4>
                    <p class="c-section__description">{{trans('networks.settings.section_help')}}</p>
                
            
                    <div>                
                        <input type="checkbox" name="public_core_enabled" id="public_core_enabled" value="true" @if(isset($public_core_enabled) && $public_core_enabled) checked @endif /><label for="public_core_enabled">{{trans('networks.settings.enabled')}}</label>
                    </div>

                    <div class="c-form__field">
                        
                        <div class="c-form__field">
                        <label for="public_core_url">{{trans('networks.settings.url')}}</label>
                        @if( isset($errors) && $errors->has('public_core_url') )
                            <span class="field-error">{{ implode(",", $errors->get('public_core_url'))  }}</span>
                        @endif
                        <input class="c-form__input c-form__input--larger" type="text" required name="public_core_url" id="public_core_url" value="{{old('public_core_url', isset($public_core_url) ? $public_core_url : '')}}">
                        </div>

                        <div class="c-form__field">
                        <label for="public_core_password">{{trans('networks.settings.password')}}</label>
                        @if( isset($errors) && $errors->has('public_core_password') )
                            <span class="field-error">{{ implode(",", $errors->get('public_core_password'))  }}</span>
                        @endif
                        <input class="c-form__input" type="password" required name="public_core_password" id="public_core_password" value="{{old('public_core_password', isset($public_core_password) ? $public_core_password : '')}}">
                        </div>
                    </div>
                    
                    <div class="c-form__field">

                        <strong>{{ trans('networks.settings.name_section') }}</strong><br/>
                        <p class="c-section__description">{{ trans('networks.settings.name_section_help') }}</span>

                        <div class="c-form__field">
                        <label for="public_core_network_name_en">{{trans('networks.settings.name_en')}}</label>
                        @if( isset($errors) && $errors->has('public_core_network_name_en') )
                            <span class="field-error">{{ implode(",", $errors->get('public_core_network_name_en'))  }}</span>
                        @endif
                        <input class="c-form__input" type="text" name="public_core_network_name_en" id="public_core_network_name_en" value="{{old('public_core_network_name_en', isset($public_core_network_name_en) ? $public_core_network_name_en : '')}}">
                        </div>

                        <div class="c-form__field">
                        <label for="public_core_network_name_ru">{{trans('networks.settings.name_ru')}}</label>
                        @if( isset($errors) && $errors->has('public_core_network_name_ru') )
                            <span class="field-error">{{ implode(",", $errors->get('public_core_network_name_ru'))  }}</span>
                        @endif
                        <input class="c-form__input" type="text" name="public_core_network_name_ru" id="public_core_network_name_ru" value="{{old('public_core_network_name_ru', isset($public_core_network_name_ru) ? $public_core_network_name_ru : '')}}">
                        </div>


                    </div>
                    
                    <div class="c-form__field">

                        <strong>{{ trans('networks.settings.streaming_section') }}</strong><br/>
                        <p class="c-section__description">{{ trans('networks.settings.streaming_section_help') }}</span>

                        <div class="c-form__field">
                        <label for="streaming_service_url">{{trans('networks.settings.streaming_service_url')}}</label>
                        @if( isset($errors) && $errors->has('streaming_service_url') )
                            <span class="field-error">{{ implode(",", $errors->get('streaming_service_url'))  }}</span>
                        @endif
                        <input class="c-form__input c-form__input--larger" type="text" name="streaming_service_url" id="streaming_service_url" value="{{old('streaming_service_url', isset($streaming_service_url) ? $streaming_service_url : '')}}">
                        </div>

                    </div>
                    
                    <div class="c-form__buttons">
                        <button type="submit" class="button" id="public-settings-save-btn" name="public-settings-save-btn">
                            {{trans('administration.settings.save_btn')}}
                        </button>
                    </div>

                </div>
            
            </form>
        


            <form  method="post" class="c-form" action="{{route('administration.settings.store')}}">
    
                @csrf

                <div class="c-section">
                    <h4 class="c-section__title">{{trans('administration.settings.analytics_section')}}</h4>
                    <p class="c-section__description">{{trans('administration.settings.analytics_section_help')}}</p> 
            
                    <div class="c-form__field">
                        <label for="analytics_token">{{trans('administration.settings.analytics_token_field')}}</label>
                        @if( isset($errors) && $errors->has('analytics_token') )
                            <span class="field-error">{{ implode(",", $errors->get('analytics_token'))  }}</span>
                        @endif
                        <input class="c-form__input" type="text" name="analytics_token" id="analytics_token" value="{{old('analytics_token', isset($analytics_token) ? $analytics_token : '')}}">
                    </div>
                
                    <div class="c-form__buttons">
                        <button type="submit" class="button" id="analytics-settings-save-btn" name="analytics-settings-save-btn">
                            {{trans('administration.settings.analytics_save_btn')}}
                        </button>
                    </div>
            
                </div>
            </form>
        

    
@stop
@extends('management-layout')

@section('sub-header')
        
    <a href="{{route('administration.index')}}" class="parent">{{trans('administration.page_title')}}</a> {{$pagetitle}}

@stop

@section('action-menu')


<!--<div class="action-group">
    <a href="{{ route('administration.institutions.create') }}" class="button">
        <span class="btn-icon icon-content-white icon-content-white-ic_add_circle_outline_white_24dp"></span>{{trans('administration.institutions.create_institutions_btn')}}
    </a>
    
</div>-->


@stop

@section('content')

<div class="row">

    <div class="two columns">

        @include('administration.adminmenu', ['small' => true, 'compact' => true])

    </div>

    <div class="ten columns ">
        
        @include('errors.list')

        <div class="widget">

            <form  method="post" action="{{route('administration.settings.store')}}">
    
                <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
                
                
                
                <h3>{{trans('administration.settings.viewing_section')}}</h3>
    
                <p class="description">{{trans('administration.settings.viewing_section_help')}}</p> 
            
                <p>                
                    <input type="checkbox" name="map_visualization" id="map_visualization" value="true" @if(isset($map_visualization) && $map_visualization) checked @endif /><label for="map_visualization">{{trans('administration.settings.map_visualization_chk')}}</label>
                </p>
                
                <button type="submit" class="button" name="map-settings-save-btn"  id="map-settings-save-btn">
                    {{trans('administration.settings.save_btn')}}
                </button>
            
            </form>
        
        </div>
        
        <div class="widget">

            <form  method="post" action="{{route('administration.settings.store')}}">
    
                <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
                
                
                
                <h3>{{trans('administration.settings.support_section')}}</h3>
    
                <p class="description">{{trans('administration.settings.support_section_help')}}</p> 
            
                <p>                
                    <label for="support_token">{{trans('administration.settings.support_token_field')}}</label>
                    @if( isset($errors) && $errors->has('support_token') )
                        <span class="field-error">{{ implode(",", $errors->get('support_token'))  }}</span>
                    @endif
                    <input type="text" name="support_token" id="support_token" value="{{old('support_token', isset($support_token) ? $support_token : '')}}">
                </p>
                
                <button type="submit" class="button" id="support-settings-save-btn" name="support-settings-save-btn">
                    {{trans('administration.settings.support_save_btn')}}
                </button>
            
            </form>
        
        </div>
        
        
        <div class="widget">

            <form  method="post" action="{{route('administration.settings.store')}}">
    
                <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
                
                
                
                <h3>{{trans('networks.settings.section')}}</h3>
    
                <p class="description">{{trans('networks.settings.section_help')}}</p> 
            
                <p>                
                    <input type="checkbox" name="public_core_enabled" id="public_core_enabled" value="true" @if(isset($public_core_enabled) && $public_core_enabled) checked @endif /><label for="public_core_enabled">{{trans('networks.settings.enabled')}}</label>
                </p>

                <p>
                    
                    <label for="public_core_url">{{trans('networks.settings.url')}}</label>
                    @if( isset($errors) && $errors->has('public_core_url') )
                        <span class="field-error">{{ implode(",", $errors->get('public_core_url'))  }}</span>
                    @endif
                    <input type="text" required name="public_core_url" id="public_core_url" value="{{old('public_core_url', isset($public_core_url) ? $public_core_url : '')}}">

                    <label for="public_core_username">{{trans('networks.settings.username')}}</label>
                    @if( isset($errors) && $errors->has('public_core_username') )
                        <span class="field-error">{{ implode(",", $errors->get('public_core_username'))  }}</span>
                    @endif
                    <input type="text" required name="public_core_username" id="public_core_username" value="{{old('public_core_username', isset($public_core_username) ? $public_core_username : '')}}">

                    <label for="public_core_password">{{trans('networks.settings.password')}}</label>
                    @if( isset($errors) && $errors->has('public_core_password') )
                        <span class="field-error">{{ implode(",", $errors->get('public_core_password'))  }}</span>
                    @endif
                    <input type="password" required name="public_core_password" id="public_core_password" value="{{old('public_core_password', isset($public_core_password) ? $public_core_password : '')}}">
                </p>
                
                <div>

                    
                    <strong>{{ trans('networks.settings.name_section') }}</strong><br/>
                    <span class="description">{{ trans('networks.settings.name_section_help') }}</span>

                    <p>
                    <label for="public_core_network_name_en">{{trans('networks.settings.name_en')}}</label>
                    @if( isset($errors) && $errors->has('public_core_network_name_en') )
                        <span class="field-error">{{ implode(",", $errors->get('public_core_network_name_en'))  }}</span>
                    @endif
                    <input type="text" required name="public_core_network_name_en" id="public_core_network_name_en" value="{{old('public_core_network_name_en', isset($public_core_network_name_en) ? $public_core_network_name_en : '')}}">

                    <label for="public_core_network_name_ru">{{trans('networks.settings.name_ru')}}</label>
                    @if( isset($errors) && $errors->has('public_core_network_name_ru') )
                        <span class="field-error">{{ implode(",", $errors->get('public_core_network_name_ru'))  }}</span>
                    @endif
                    <input type="text" required name="public_core_network_name_ru" id="public_core_network_name_ru" value="{{old('public_core_network_name_ru', isset($public_core_network_name_ru) ? $public_core_network_name_ru : '')}}">

                    </p>


                </div>
                
                <p>                
                    <input type="checkbox" name="public_core_debug" id="public_core_debug" value="true" @if(isset($public_core_debug) && $public_core_debug) checked @endif /><label for="public_core_debug">{{trans('networks.settings.debug_enabled')}}</label>
                </p>
                
                <button type="submit" class="button" id="public-settings-save-btn" name="public-settings-save-btn">
                    {{trans('administration.settings.save_btn')}}
                </button>
            
            </form>
        
        </div>

    </div>

</div>

@stop
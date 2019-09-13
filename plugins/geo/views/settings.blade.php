@extends('administration.layout')

@section('breadcrumbs')
        
    <a href="{{route('administration.index')}}"  class="breadcrumb__item">@lang('administration.page_title')</a>
    <a href="{{route('administration.plugins.index')}}"  class="breadcrumb__item">@lang('plugins.page_title')</a>
    <span class="breadcrumb__item--current">{{$pagetitle}}</span>

@stop

@section('page')
        
    @include('errors.list')
    
    <form  method="post" class="" action="{{route('plugins.k-box-kbox-plugin-geo.settings.store')}}">
    
        {{ csrf_field() }}
        {{ method_field('PUT') }}

        <div class=" ">
            <h4 class="my-4">@lang('geo::settings.page_title')</h4>
            <p class="form-description">@lang('geo::settings.description')</p>

            @if($enabled)
                <span class="badge @if($connected) success @else failed @endif">{{ $connected ? trans('geo::settings.connection.established', ['version' => $version]) : trans('geo::settings.connection.failed', ['error' => $error])}}</span>
            @endif
            
            @if($gdal_version)
                <span class="badge success">{{ trans('geo::settings.gdal.available', ['version' => $gdal_version]) }}</span>
            @else
                <span class="badge failed">{{ trans('geo::settings.gdal.not_available') }}</span>
            @endif
        </div>
        <div class=" ">
            <h4 class="my-4">@lang('geo::settings.geoserver.title')</h4>
            <p class="form-description">@lang('geo::settings.geoserver.description')</p>

            <div class=" mb-4">
                
                <div class=" mb-4">
                <label for="geoserver_url">@lang('geo::settings.geoserver.url')</label>
                @if( isset($errors) && $errors->has('geoserver_url') )
                    <span class="field-error">{{ implode(",", $errors->get('geoserver_url'))  }}</span>
                @endif
                <input class="form-input block w-2/3" type="text" required name="geoserver_url" id="geoserver_url" value="{{old('geoserver_url', isset($geoserver_url) ? $geoserver_url : '')}}">
                </div>

                <div class=" mb-4">
                <label for="geoserver_username">@lang('geo::settings.geoserver.username')</label>
                @if( isset($errors) && $errors->has('geoserver_username') )
                    <span class="field-error">{{ implode(",", $errors->get('geoserver_username'))  }}</span>
                @endif
                <input class="form-input block" type="text" required name="geoserver_username" id="geoserver_username" value="{{old('geoserver_username', isset($geoserver_username) ? $geoserver_username : '')}}">
                </div>

                <div class=" mb-4">
                <label for="geoserver_password">@lang('geo::settings.geoserver.password')</label>
                @if( isset($errors) && $errors->has('geoserver_password') )
                    <span class="field-error">{{ implode(",", $errors->get('geoserver_password'))  }}</span>
                @endif
                <input class="form-input block" type="password" required name="geoserver_password" id="geoserver_password" value="{{old('geoserver_password', isset($geoserver_password) ? $geoserver_password : '')}}">
                </div>
                
                <div class=" mb-4">
                <label for="geoserver_workspace">@lang('geo::settings.geoserver.workspace')</label>
                @if( isset($errors) && $errors->has('geoserver_workspace') )
                    <span class="field-error">{{ implode(",", $errors->get('geoserver_workspace'))  }}</span>
                @endif
                <input class="form-input block" type="text" required name="geoserver_workspace" id="geoserver_workspace" value="{{old('geoserver_workspace', isset($geoserver_workspace) ? $geoserver_workspace : 'kbox')}}">
                </div>
            </div>
            
            
            <div class="c-form__buttons">
                <button type="submit" class="button" id="geoserver-save-btn" name="geoserver-save-btn">
                    {{trans('administration.settings.save_btn')}}
                </button>
            </div>

        </div>
    
    </form>

    <div class=" ">
        <h4><a class="my-4" href="{{route('plugins.k-box-kbox-plugin-geo.mapproviders')}}">{{ trans('geo::settings.providers.title') }}</a></h4>
        <p class="form-description">@lang('geo::settings.providers.description')</p>
    </div>

@stop
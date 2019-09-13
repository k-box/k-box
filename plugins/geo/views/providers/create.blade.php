@extends('administration.layout')

@section('breadcrumbs')
        
    <a href="{{route('administration.index')}}"  class="breadcrumb__item">@lang('administration.page_title')</a>
    <a href="{{route('administration.plugins.index')}}"  class="breadcrumb__item">@lang('plugins.page_title')</a>
    <a href="{{ route('plugins.k-box-kbox-plugin-geo.settings') }}"  class="breadcrumb__item"><span class="breadcrumb__item--current">{{$plugintitle}}</a>
    <a href="{{ route('plugins.k-box-kbox-plugin-geo.mapproviders') }}"  class="breadcrumb__item"><span class="breadcrumb__item--current">@lang('geo::settings.providers.title')</a>
    <span class="breadcrumb__item--current">{{$pagetitle}}</span>

@stop

@section('page')
        
    @include('errors.list')
    
    <form  method="post" class="" action="{{route('plugins.k-box-kbox-plugin-geo.mapproviders.store')}}">
    
        {{ csrf_field() }}
        

        <div class=" ">
            <h4 class="mt-4">@lang('geo::settings.providers.create_title')</h4>
            <p class="form-description">@lang('geo::settings.providers.create_description')</p>

            @include('geo::providers.form')
            
            <div class="c-form__buttons">
                <button type="submit" class="button" id="providers-create-btn" name="providers-create-btn">
                    {{trans('geo::settings.providers.create_title')}}
                </button>
            </div>

        </div>
    
    </form>

@stop
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
    
    <form  method="post" class="" action="{{route('plugins.k-box-kbox-plugin-geo.mapproviders.update', ['id' => $providerId])}}">
    
        {{ csrf_field() }}
        {{ method_field('PUT') }}

        <div class=" ">
            <h4 class="my-4">{{$pagetitle}}</h4>
            <p class="form-description">@lang('geo::settings.providers.edit_description')</p>

            @include('geo::providers.form')
            
            <div class="c-form__buttons">
                <button type="submit" class="button" id="providers-save-btn" name="providers-save-btn">
                    {{trans('administration.settings.save_btn')}}
                </button>
            </div>

        </div>
    
    </form>

@stop
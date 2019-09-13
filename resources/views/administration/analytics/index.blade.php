@extends('administration.layout')

@section('breadcrumbs')
        
    <a href="{{route('administration.index')}}"  class="breadcrumb__item">{{trans('administration.page_title')}}</a> <span class="breadcrumb__item--current">{{$pagetitle}}</span>

@stop

@section('action-menu')




@stop

@section('page')
        
    @include('errors.list')
    

    <form  method="post" class="" action="{{route('administration.analytics.update')}}">

        {{ csrf_field() }}
        
        @method('PUT')

        <div class=" ">
            <h4 class="my-4">{{trans('administration.analytics.section')}}</h4>
            <p class="form-description">{{trans('administration.analytics.section_help')}}</p> 

            <div class=" mb-4">
                <label for="analytics_service">{{trans('administration.analytics.service_field')}}</label>
                @if( isset($errors) && $errors->has('analytics_service') )
                    <span class="field-error">{{ implode(",", $errors->get('analytics_service'))  }}</span>
                @endif
                
                <select class="form-select block mt-1" name="analytics_service" id="analytics_service">
                    @foreach ($available_services as $service)
                        <option value="{{ $service }}" @if(old('analytics_service', $analytics_service ?? '')===$service ) selected @endif>{{ $service }}</option>
                    @endforeach
                </select>
            </div>
    
            
            <div class=" mb-4 js-analytics-domain">
                <label for="analytics_domain">{{trans('administration.analytics.domain_field')}}</label>
                @if( isset($errors) && $errors->has('analytics_domain') )
                    <span class="field-error">{{ implode(",", $errors->get('analytics_domain'))  }}</span>
                @endif
                <input class="form-input block mt-1" type="text" name="analytics_domain" id="analytics_domain" value="{{old('analytics_domain', isset($analytics_domain) ? $analytics_domain : '')}}">
            </div>


            <div class=" mb-4">
                <label for="analytics_token">{{trans('administration.analytics.token_field')}}</label>
                @if( isset($errors) && $errors->has('analytics_token') )
                    <span class="field-error">{{ implode(",", $errors->get('analytics_token'))  }}</span>
                @endif
                <input class="form-input block mt-1" type="text" name="analytics_token" id="analytics_token" value="{{old('analytics_token', isset($analytics_token) ? $analytics_token : '')}}">
            </div>
    
        
            <div class="c-form__buttons">
                <button type="submit" class="button" id="analytics-settings-save-btn" name="analytics-settings-save-btn">
                    {{trans('administration.analytics.save_btn')}}
                </button>
            </div>
    
        </div>
    </form>
    
@stop
@extends('administration.layout')

@section('breadcrumbs')
        
    <a href="{{route('administration.index')}}"  class="breadcrumb__item">{{trans('administration.page_title')}}</a> <span class="breadcrumb__item--current">{{$pagetitle}}</span>

@stop

@section('action-menu')




@stop

@section('page')
        
    @include('errors.list')
    

    <form  method="post" class="c-form" action="{{route('administration.analytics.update')}}">

        {{ csrf_field() }}
        
        @method('PUT')

        <div class="c-section">
            <h4 class="c-section__title">{{trans('administration.analytics.section')}}</h4>
            <p class="c-section__description">{{trans('administration.analytics.section_help')}}</p> 

            <div class="c-form__field">
                <label for="analytics_service">{{trans('administration.analytics.service_field')}}</label>
                @if( isset($errors) && $errors->has('analytics_service') )
                    <span class="field-error">{{ implode(",", $errors->get('analytics_service'))  }}</span>
                @endif
                
                <select class="c-form__input" name="analytics_service" id="analytics_service">
                    @foreach ($available_services as $service)
                        <option value="{{ $service }}" @if(old('analytics_service', $analytics_service ?? '')===$service ) selected @endif>{{ $service }}</option>
                    @endforeach
                </select>
            </div>
    
            
            <div class="c-form__field js-analytics-domain">
                <label for="analytics_domain">{{trans('administration.analytics.domain_field')}}</label>
                @if( isset($errors) && $errors->has('analytics_domain') )
                    <span class="field-error">{{ implode(",", $errors->get('analytics_domain'))  }}</span>
                @endif
                <input class="c-form__input" type="text" name="analytics_domain" id="analytics_domain" value="{{old('analytics_domain', isset($analytics_domain) ? $analytics_domain : '')}}">
            </div>


            <div class="c-form__field">
                <label for="analytics_token">{{trans('administration.analytics.token_field')}}</label>
                @if( isset($errors) && $errors->has('analytics_token') )
                    <span class="field-error">{{ implode(",", $errors->get('analytics_token'))  }}</span>
                @endif
                <input class="c-form__input" type="text" name="analytics_token" id="analytics_token" value="{{old('analytics_token', isset($analytics_token) ? $analytics_token : '')}}">
            </div>
    
        
            <div class="c-form__buttons">
                <button type="submit" class="button" id="analytics-settings-save-btn" name="analytics-settings-save-btn">
                    {{trans('administration.analytics.save_btn')}}
                </button>
            </div>
    
        </div>
    </form>
    
@stop
@extends('administration.layout')

@section('breadcrumbs')
        
    <a href="{{route('administration.index')}}"  class="breadcrumb__item">{{trans('administration.page_title')}}</a> <span class="breadcrumb__item--current">{{$pagetitle}}</span>

@stop

@section('action-menu')




@stop

@section('page')
        
    @include('errors.list')
    

    <form  method="post" class="" action="{{route('administration.support.update')}}">

        {{ csrf_field() }}
        
        @method('PUT')

        <div class=" ">
            <h4 class="my-4">{{trans('administration.support.section')}}</h4>
            <p class="form-description">{{trans('administration.support.section_help')}}</p> 

            <div class=" mb-4">
                <label for="support_token">{{trans('administration.support.token_field')}}</label>
                @if( isset($errors) && $errors->has('support_token') )
                    <span class="field-error">{{ implode(",", $errors->get('support_token'))  }}</span>
                @endif
                <input class="form-input block" type="text" name="support_token" id="support_token" value="{{old('support_token', isset($support_token) ? $support_token : '')}}">
            </div>
    
        
            <div class="c-form__buttons">
                <button type="submit" class="button" id="support-settings-save-btn" name="support-settings-save-btn">
                    {{trans('administration.support.save_btn')}}
                </button>
            </div>
    
        </div>
    </form>
    
@stop
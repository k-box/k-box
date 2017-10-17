@extends('administration.layout')

@section('breadcrumbs')
        
    <a href="{{route('administration.index')}}"  class="breadcrumb__item">{{trans('administration.page_title')}}</a> <span class="breadcrumb__item--current">{{trans('administration.menu.network')}}</span>

@stop

@section('action-menu')

@stop

@section('page')

    <div class="c-section">
        <h4 class="c-section__title">{{trans('administration.network.ksearch')}}</h4>
        <p class="c-section__description">{{trans('administration.network.ksearch_description')}}</p>

        <div class="c-form__field">

            <span class="badge {{$local_connection}}">{{trans('administration.network.klink_status.' . $local_connection)}}</span>

            @if($local_connection_error)
                {{$local_connection_error}}
            @endif
        </div>
    </div>

    @if(network_enabled())

    <div class="c-section">
        <h4 class="c-section__title">{{trans('administration.network.network', ['network' => network_name()])}}</h4>
        <p class="c-section__description">{{trans('administration.network.network_description')}}</p>

        <div class="c-form__field">

            <span class="badge {{$remote_connection}}">{{trans('administration.network.klink_status.' . $remote_connection)}}</span>

            @if($remote_connection_error)
                {{$remote_connection_error}}
            @endif
        </div>
    </div>

    @endif

@stop
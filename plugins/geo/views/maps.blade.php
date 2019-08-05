@extends('administration.layout')

@section('breadcrumbs')
        
    <a href="{{route('administration.index')}}"  class="breadcrumb__item">@lang('administration.page_title')</a>
    <a href="{{route('administration.plugins.index')}}"  class="breadcrumb__item">@lang('plugins.page_title')</a>
    <a href="{{ route('plugins.k-box-kbox-plugin-geo.settings') }}"  class="breadcrumb__item"><span class="breadcrumb__item--current">{{$plugintitle}}</a>
    <span class="breadcrumb__item--current">{{$pagetitle}}</span>

@stop

@section('page')
        
    @include('errors.list')
    
    <div class="c-section">
        <h4 class="c-section__title">@lang('geo::settings.providers.title')</h4>
        <p class="c-section__description">@lang('geo::settings.providers.description')</p>

        <div class="c-form__buttons">
            <a class="button" href="{{route('plugins.k-box-kbox-plugin-geo.mapproviders.create')}}">
                @lang('geo::settings.providers.create_title')
            </a>
        </div>

        <table class="c-table" style="margin-top:12px">
            <thead class="c-table__head">
                <tr>
                    <th style="width:6%">{{trans('geo::settings.providers.attributes.default')}}</th>
                    <th style="width:8%">{{trans('geo::settings.providers.attributes.enabled')}}</th>
                    <th style="width:45%">{{trans('geo::settings.providers.attributes.label')}}</th>
                    <th style="width:15%">{{trans('geo::settings.providers.attributes.type')}}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
        
                @foreach ($providers as $providerId => $provider)

                    <tr>
                        <td>
                            @if( $default == $providerId)
                                <button class="button button--ghost" >
                                    @materialicon('action', 'check_circle', 'inline-block')
                                </button>    
                            @else 
                                <button class="button button--ghost" onclick="event.preventDefault();document.getElementById('provider-default-{{$providerId}}').submit();">@materialicon('toggle', 'radio_button_unchecked', 'inline-block')</button>
                                <form id="provider-default-{{$providerId}}" action="{{ route('plugins.k-box-kbox-plugin-geo.mapproviders.default.update') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                    {{ method_field('PUT') }}
                                    <input type="hidden" name="default" value="{{$providerId}}">
                                </form>
                            @endif
                        </td>
                        <td>
                            @if( $provider['enable'] ?? false )
                                <button class="button button--ghost" onclick="event.preventDefault();document.getElementById('provider-enable-{{$providerId}}').submit();">@materialicon('toggle', 'check_box', 'inline-block')</button>
                                <form id="provider-enable-{{$providerId}}" action="{{ route('plugins.k-box-kbox-plugin-geo.mapproviders.enable.update') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                    {{ method_field('PUT') }}
                                    <input type="hidden" name="provider" value="{{$providerId}}">
                                    <input type="hidden" name="enable" value="0">
                                </form>
                            @else 
                                <button class="button button--ghost" onclick="event.preventDefault();document.getElementById('provider-enable-{{$providerId}}').submit();">@materialicon('toggle', 'check_box_outline_blank', 'inline-block')</button>
                                <form id="provider-enable-{{$providerId}}" action="{{ route('plugins.k-box-kbox-plugin-geo.mapproviders.enable.update') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                    {{ method_field('PUT') }}
                                    <input type="hidden" name="provider" value="{{$providerId}}">
                                    <input type="hidden" name="enable" value="1">
                                </form>
                            @endif
                        </td>
                        <td>{{ $provider['label'] ?? '' }}</td>
                        <td>{{ $provider['type'] }}</td>
                        <td>
                            <a class="button" href="{{route('plugins.k-box-kbox-plugin-geo.mapproviders.edit', ['id' => $providerId])}}">
                                @lang('actions.edit')
                            </a>
                            <button class="button button--danger" onclick="event.preventDefault();document.getElementById('provider-destroy-{{$providerId}}').submit();">@lang('actions.dialogs.delete_btn')</button>
                            <form id="provider-destroy-{{$providerId}}" action="{{ route('plugins.k-box-kbox-plugin-geo.mapproviders.delete', ['id' => $providerId]) }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                            </form>
                        </td>
                    </tr>

                @endforeach
            </tbody>
        </table>
        
    </div>

@stop
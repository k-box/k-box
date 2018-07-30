@extends('administration.layout')

@section('breadcrumbs')
        
    <a href="{{route('administration.index')}}"  class="breadcrumb__item">{{trans('administration.page_title')}}</a> 
    <span class="breadcrumb__item--current">{{$pagetitle}}</span>

@stop

@section('page')
        
        @include('errors.list')

        <div class="c-message c-message--warning">
            {{ trans('plugins.experimental_notice') }}
        </div>

        <table class="c-table">
            <thead class="c-table__head">
                <tr>
                    <th style="width:40%">{{trans('plugins.table.name_column')}}</th>
                    <th style="width:30%">{{trans('plugins.table.creator_column')}}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>

                <tr>
                    <td>
                        <strong>Geo</strong> (version 0.1)
                        <br/><span class="description">Enable the support for GeoJSON, KML, Shapefile via the connection to a GeoServer</span>
                    </td>
                    <td>Alessio Vertemati (OneOffTech)</td>
                    <td>
                        @if (flags()->isGeoPluginEnabled())
                        
                            <button disabled class="button" href="{{ route('administration.plugins.edit', 'geo') }}">{{trans('plugins.actions.settings')}}</button>

                            <button class="button button--danger" onclick="event.preventDefault();document.getElementById('plugin-disable-form').submit();">{{trans('plugins.actions.disable')}}</button>
                            <form id="plugin-disable-form" action="{{ route('administration.plugins.destroy', 'geo') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                            </form>
                        @else
                            <button class="button" onclick="event.preventDefault();document.getElementById('plugin-enable-form').submit();">{{trans('plugins.actions.enable')}}</button>
                            <form id="plugin-enable-form" action="{{ route('administration.plugins.update', 'geo') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                                {{ method_field('PUT') }}
                            </form>
						</div>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

@stop
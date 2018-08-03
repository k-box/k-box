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

                @foreach ($plugins as $plugin)
                    
                    <tr>
                        <td>
                            <strong>{{ $plugin->name }}</strong>
                            <br/><span class="description">{{ $plugin->description }}</span>
                        </td>
                        <td>{{ collect($plugin->authors)->pluck('name')->implode(', ') }}</td>
                        <td>
                            @if ($plugin->enabled)
                            
                                {{-- <button disabled class="button" href="{{ route('administration.plugins.edit', $plugin->name) }}">{{trans('plugins.actions.settings')}}</button> --}}

                                <button class="button button--danger" onclick="event.preventDefault();document.getElementById('plugin-disable-form-{{str_slug($plugin->name)}}').submit();">{{trans('plugins.actions.disable')}}</button>
                                <form id="plugin-disable-form-{{str_slug($plugin->name)}}" action="{{ route('administration.plugins.destroy', $plugin->name) }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                </form>
                            @else
                                <button class="button" onclick="event.preventDefault();document.getElementById('plugin-enable-form-{{str_slug($plugin->name)}}').submit();">{{trans('plugins.actions.enable')}}</button>
                                <form id="plugin-enable-form-{{str_slug($plugin->name)}}" action="{{ route('administration.plugins.update', $plugin->name) }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                    {{ method_field('PUT') }}
                                </form>
                            </div>
                            @endif
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>

@stop
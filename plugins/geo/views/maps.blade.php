@extends('administration.layout')

@section('breadcrumbs')
        
    <a href="{{route('administration.index')}}"  class="breadcrumb__item">@lang('administration.page_title')</a>
    <a href="{{route('administration.plugins.index')}}"  class="breadcrumb__item">@lang('plugins.page_title')</a>
    <a href="{{ route('plugins.k-box-kbox-plugin-geo.settings') }}"  class="breadcrumb__item"><span class="breadcrumb__item--current">{{$plugintitle}}</a>
    <span class="breadcrumb__item--current">{{$pagetitle}}</span>

@stop

@section('page')
        
    @include('errors.list')
    
    {{-- <form  method="post" class="c-form" action="{{route('plugins.k-box-kbox-plugin-geo.mapproviders.store')}}"> --}}
    
        {{-- {{ csrf_field() }}
        {{ method_field('PUT') }} --}}

        
        <div class="c-section">
            <h4 class="c-section__title">@lang('geo::settings.providers.title')</h4>
            <p class="c-section__description">@lang('geo::settings.providers.description')</p>

            <div class="c-form__buttons">
                <a class="button" href="{{route('plugins.k-box-kbox-plugin-geo.mapproviders.create')}}">
                    @lang('geo::settings.providers.create_title')
                </a>
            </div>

            <table class="c-table">
                <thead class="c-table__head">
                    <tr>
                        <th style="width:5%">{{trans('geo::settings.providers.attributes.default')}}</th>
                        <th style="width:10%">{{trans('geo::settings.providers.attributes.id')}}</th>
                        <th style="width:30%">{{trans('geo::settings.providers.attributes.label')}}</th>
                        <th style="width:5%">{{trans('geo::settings.providers.attributes.type')}}</th>
                        <th style="width:40%">{{trans('geo::settings.providers.attributes.url')}}</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
            
                    @foreach ($providers as $providerId => $provider)

                        <tr>
                            <td>
                                @if( $default == $providerId)
                                    @materialicon('action', 'check_circle')
                                @endif
                            </td>
                            <td><code>{{ $providerId }}</code></td>
                            <td>{{ $provider['label'] ?? '' }}</td>
                            <td>{{ $provider['type'] }}</td>
                            <td>{{ $provider['url'] }}</td>
                            <td>
                                <a class="button" href="{{route('plugins.k-box-kbox-plugin-geo.mapproviders.edit', ['id' => $providerId])}}">
                                    @lang('actions.edit')
                                </a>
                            </td>
                        </tr>

                    @endforeach
                </tbody>
            </table>
            
            

        </div>
    
    {{-- </form> --}}

@stop
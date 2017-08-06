@extends('administration.layout')

@section('breadcrumbs')
        
    <a href="{{route('administration.index')}}" class="breadcrumb__item">{{trans('administration.page_title')}}</a> <span class="breadcrumb__item--current">{{trans('administration.menu.maintenance')}}</span>

@stop

@section('action-menu')


@stop

@section('page')
        
        <div class="c-section c-section--separated">
            <h4 class="c-section__title">{{trans('administration.maintenance.queue_runner')}}</h4>

            <p>
                <span class="badge {{$queue_runner_status_class}}">{{$queue_runner_status}}</span>
            </p>

            @if(!$queue_runner_status_boolean)

                <p class="c-section__description">
                    {{trans('administration.maintenance.queue_runner_not_running_description')}}
                </p>

            @endif
        
        </div>
        
        
        <div class="c-section">
            <h4 class="c-section__title">{{trans('administration.maintenance.logs_widget_title')}}</h4>

            <div class="log">
                {{$log_entries}}
            </div>
        
        </div>

@stop
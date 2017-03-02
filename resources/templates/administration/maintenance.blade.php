@extends('management-layout')

@section('sub-header')
        
    <a href="{{route('administration.index')}}" class="parent">{{trans('administration.page_title')}}</a> {{trans('administration.menu.maintenance')}}

@stop

@section('action-menu')


<div class="action-group">
    {{-- <a href="{{ route('administration.users.create') }}" class="button">
        <span class="btn-icon icon-social-white icon-social-white-ic_person_add_white_24dp"></span>Create User
    </a> --}}
</div>


@stop

@section('content')

<div class="row">

    <div class="two columns">

        @include('administration.adminmenu')

    </div>

    <div class="ten columns">
        
        <div class="widget">
            
            <h4>{{trans('administration.maintenance.queue_runner')}}</h4>

            <span class="badge {{$queue_runner_status_class}}">{{$queue_runner_status}}</span>

            @if(!$queue_runner_status_boolean)

                <p class="description">
                    {{trans('administration.maintenance.queue_runner_not_running_description')}}
                </p>

            @endif
        
        </div>
        
        
        <div class="widget">
            
            <h4>{{trans('administration.maintenance.logs_widget_title')}}</h4>

            <pre><code>{{$log_entries}}</code></pre>
        
        </div>

    </div>

    <!--<div class="five columns ">

        &nbsp;

    </div>-->

    

</div>

@stop
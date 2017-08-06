@extends('administration.layout')

@section('breadcrumbs')
        
    <a href="{{route('administration.index')}}"  class="breadcrumb__item">{{trans('administration.page_title')}}</a> <span class="breadcrumb__item--current">{{trans('administration.menu.network')}}</span>

@stop

@section('action-menu')


<div class="action-group">
    {{-- <a href="{{ route('administration.users.create') }}" class="button">
        <span class="btn-icon icon-social-white icon-social-white-ic_person_add_white_24dp"></span>Create User
    </a> --}}
</div>


@stop

@section('page')

        <div class="widget klink-network">

            <h5>{{trans('administration.network.klink_net_title')}}</h5>

            <span class="badge {{$klink_network_connection}}">{{trans('administration.network.klink_status.' . $klink_network_connection)}}</span>

            @if($klink_network_connection_error)
                {{$klink_network_connection_error->getMessage()}}
            @endif
        </div>


@stop
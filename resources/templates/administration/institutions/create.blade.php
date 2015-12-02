@extends('management-layout')

@section('sub-header')

    <a href="{{route('administration.index')}}" class="parent">{{trans('administration.page_title')}}</a> <a href="{{route('administration.institutions.index')}}" class="parent">{{trans('administration.menu.institutions')}}</a> {{trans('administration.institutions.create_title')}}

@stop

@section('content')

    <h3>{{trans('administration.institutions.create_title')}}</h3>

    <form  method="post" action="{{route('administration.institutions.store')}}">
        
        

        @include('administration.institutions.form', ['submit_text' => trans('administration.institutions.labels.create'), 'can_change_klink_id' => true])
        
    </form>
@stop
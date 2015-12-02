@extends('management-layout')

@section('sub-header')

    <a href="{{route('administration.index')}}" class="parent">{{trans('administration.page_title')}}</a> <a href="{{route('administration.users.index')}}" class="parent">{{trans('administration.menu.accounts')}}</a> {{trans('administration.accounts.create.slug')}}

@stop

@section('content')

    <h3>{{trans('administration.accounts.create.title')}}</h3>

    <form  method="post" action="{{route('administration.users.store')}}">
        
        

        @include('administration.users.form', ['submit_text' => trans('administration.accounts.labels.create'), 'caps' => []])
        
    </form>
@stop
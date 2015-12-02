@extends('management-layout')

@section('sub-header')

    <a href="{{route('administration.index')}}" class="parent">{{trans('administration.page_title')}}</a> <a href="{{route('administration.users.index')}}" class="parent">Users</a> Create

@stop

@section('content')

    <form  method="post">
        
    </form>
@stop
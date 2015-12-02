@extends('management-layout')

@section('sub-header')

    <a href="{{route('administration.index')}}" class="parent">Administration</a> <a href="{{route('administration.institutions.index')}}" class="parent">{{trans('administration.menu.institutions')}}</a> {{$institution->name}}

@stop

@section('content')

    <h3>{{trans('administration.institutions.edit_title', ['name' => $institution->name])}}</h3>

    <form  method="POST" action="{{route('administration.institutions.update', $institution->id)}}">

        <input type="hidden" name="_method" value="PUT">


        @include('administration.institutions.form', ['submit_text' => trans('administration.institutions.labels.update'), 'can_change_mail' => false])

    </form>
@stop
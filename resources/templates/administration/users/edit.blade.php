@extends('management-layout')

@section('sub-header')

    <a href="{{route('administration.index')}}" class="parent">Administration</a> <a href="{{route('administration.users.index')}}" class="parent">{{trans('administration.menu.accounts')}}</a> {{$user->name}}

@stop

@section('content')

    <h3>{{trans('administration.accounts.edit_account_title', ['name' => $user->name])}}</h3>

    <form  method="POST" action="{{route('administration.users.update', $user->id)}}">

        <input type="hidden" name="_method" value="PUT">


        @include('administration.users.form', ['submit_text' => trans('administration.accounts.labels.update'), 'can_change_mail' => true])

    </form>
@stop
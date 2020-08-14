@extends('administration.layout')

@section('breadcrumbs')

    <a href="{{route('administration.index')}}"  class="breadcrumb__item">{{trans('administration.page_title')}}</a> <a href="{{route('administration.users.index')}}"  class="breadcrumb__item">{{trans('administration.menu.accounts')}}</a> <span class="breadcrumb__item--current">{{$user->name}}</span>

@stop

@section('page')

    <form  method="POST" class="" action="{{route('administration.users.update', ['user' => $user->id])}}">

        {{ method_field('PUT') }}

        <div class=" ">
            <h4 class="my-4">{{trans('administration.accounts.edit_account_title', ['name' => $user->name])}}</h4>

            @include('administration.users.form', ['submit_text' => trans('administration.accounts.labels.update'), 'can_change_mail' => true])

        </div>

    </form>
@stop
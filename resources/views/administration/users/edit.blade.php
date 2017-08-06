@extends('global')

@section('breadcrumbs')

    <a href="{{route('administration.index')}}"  class="breadcrumb__item">{{trans('administration.page_title')}}</a> <a href="{{route('administration.users.index')}}"  class="breadcrumb__item">{{trans('administration.menu.accounts')}}</a> <span class="breadcrumb__item--current">{{$user->name}}</span>

@stop

@section('content')


    <form  method="POST" class="c-form" action="{{route('administration.users.update', $user->id)}}">

        <input type="hidden" name="_method" value="PUT">

        <div class="c-section">
            <h4 class="c-section__title">{{trans('administration.accounts.edit_account_title', ['name' => $user->name])}}</h4>

            @include('administration.users.form', ['submit_text' => trans('administration.accounts.labels.update'), 'can_change_mail' => true])

        </div>

    </form>
@stop
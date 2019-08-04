@extends('administration.layout')

@section('breadcrumbs')

    <a href="{{route('administration.index')}}"  class="breadcrumb__item">{{trans('administration.page_title')}}</a> <a href="{{route('administration.users.index')}}"  class="breadcrumb__item">{{trans('administration.menu.accounts')}}</a> <span class="breadcrumb__item--current">{{trans('administration.accounts.create.slug')}}</span>

@stop

@section('page')

    <form  method="post" class="c-form" action="{{route('administration.users.store')}}">
        
        <div class="c-section">
            <h4 class="c-section__title">{{trans('administration.accounts.create.title')}}</h4>
        

            @include('administration.users.form', ['submit_text' => trans('administration.accounts.labels.create'), 'caps' => []])

        </div>
        
    </form>
@stop
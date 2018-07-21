@extends('global')

@section('breadcrumbs')

    <a href="{{route('administration.index')}}"  class="breadcrumb__item">{{trans('administration.page_title')}}</a> <a href="{{route('administration.institutions.index')}}"  class="breadcrumb__item">{{trans('administration.menu.institutions')}}</a> <span class="breadcrumb__item--current">{{$institution->name}}</span>

@stop

@section('content')


    <form  method="POST" class="c-form" action="{{route('administration.institutions.update', $institution->id)}}">
    
        <div class="c-section">
            <h4 class="c-section__title">{{trans('administration.institutions.edit_title', ['name' => $institution->name])}}</h4>

            @method('PUT')


            @include('administration.institutions.form', ['submit_text' => trans('administration.institutions.labels.update'), 'can_change_mail' => false])

        </div>

    </form>
@stop
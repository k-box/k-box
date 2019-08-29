@extends('global')

@section('breadcrumbs')

    <a href="{{route('administration.index')}}" class="breadcrumb__item">{{trans('administration.page_title')}}</a> <a href="{{route('administration.institutions.index')}}"  class="breadcrumb__item">{{trans('administration.menu.institutions')}}</a> <span class="breadcrumb__item--current">{{trans('administration.institutions.create_title')}}</span>

@stop

@section('content')


    <form method="post" class="" action="{{route('administration.institutions.store')}}">
        
        <div class=" ">
            <h4 class="my-4">{{trans('administration.institutions.create_title')}}</h4>

            @include('administration.institutions.form', ['submit_text' => trans('administration.institutions.labels.create'), 'can_change_klink_id' => true])

        </div>
        
    </form>
@stop
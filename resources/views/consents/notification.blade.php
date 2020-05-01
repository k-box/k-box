@extends('global')

@section('header')
    @include('headers.header', [
        'hide_menu' => true,
        'current_user_home_route' => $skip_to,
        'profile_url' => false,
    ])

@overwrite


@section('breadcrumbs')

	@if(isset($page_title))
		{{$page_title}}
	@elseif(isset($pagetitle))
		{{$pagetitle}}
	@endif

@stop


@section('content')

	<div class="">
	

        <form action="{{ route('consent.dialog.notification.update') }}" method="post" class="">

            {{ csrf_field() }}
            {{ method_field('PUT') }}

            <h2>{{ trans('consent.others.dialog_title') }}</h2>
            <p>{{ trans('consent.others.dialog_description') }}</p>

            <div class=" mb-4 ">

                <strong>{{trans('consent.notification.dialog_title')}}</strong>
                <p>{{trans('consent.notification.dialog_description')}}</p>
                @if( $errors->has('notifications') )
                    <span class="field-error">{{ implode(",", $errors->get('notifications'))  }}</span>
                @endif

                <input type="hidden" name="notifications" id="notifications" value="1">
                
            </div>
            

            <div class=" mb-4">
                <button class="button button--primary" type="submit">{{ trans('consent.notification.agree_label') }}</button>
                <a href="{{$skip_to}}">{{ trans('consent.skip') }}</a>
            </div>
        </form>

    </div>
    
@stop
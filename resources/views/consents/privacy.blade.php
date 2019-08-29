@extends('global')

@section('header')
    @include('headers.header', [
        'hide_menu' => true,
        'current_user_home_route' => route('consent.dialog.privacy.show'),
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

	<div class="c-page">

        <form action="{{ route('consent.dialog.privacy.update') }}" method="post" class="">

            {{ csrf_field() }}
            {{ method_field('PUT') }}

            <input type="hidden" name="agree" value="privacy">

            <h2>{{ trans('consent.privacy.dialog_title') }}</h2>
            <p>{{ trans('consent.privacy.dialog_description') }}</p>

            @if(!is_null($summary_content))

                <div class="box">
                    {!! $summary_content !!}
                </div>

                <p>
                    <a href="#legal">{{ trans('consent.privacy.show_full_text') }}</a>
                </p>

                <div class="box hide-if-not-target" id="legal">
                    {!! $privacy_content !!}
                </div>

            @else 

                <div class="box" id="legal">
                    {!! $privacy_content !!}
                </div>

            @endif

            

            <div class=" mb-4">
                <button class="button button--primary" type="submit">{{ trans('consent.agree') }}</button>
                <button class="button" onclick="event.preventDefault();document.getElementById('logout-form').submit();">{{ trans('consent.disagree_logout') }}</button>
            </div>
        </form>

    </div>
    
@stop
@extends('global')

@section('header')
    @include('headers.header', [
        'hide_menu' => true,
        'current_user_home_route' => route('consent.dialog.privacy.show'),
        'profile_url' => false,
    ])

@overwrite

@section('content')

    <div class="h-5"></div>

	<div class="max-w-4xl">

        <form action="{{ route('consent.dialog.privacy.update') }}" method="post" class="">

            {{ csrf_field() }}
            {{ method_field('PUT') }}

            <input type="hidden" name="agree" value="privacy">

            <h2>{{ trans('consent.privacy.dialog_title') }}</h2>
            <p>{{ trans('consent.privacy.dialog_description') }}</p>

            <div class="h-5"></div>

            @if(!is_null($summary_content))

                <x-markdown class="bg-gray-100 p-2">{!! $summary_content !!}</x-markdown>

                <p class="mt-4">
                    <a href="{{ route('privacy.legal') }}" target="_blank" rel="noopener noreferrer">{{ trans('consent.privacy.show_full_text') }}</a>
                </p>

            @else 

                <x-markdown class="bg-gray-100 p-2">{!! $privacy_content !!}</x-markdown>

            @endif


            <div class="my-10 flex">
                <button class="button button--primary w-1/3 sm:w-32 mr-2" type="submit">{{ trans('consent.agree') }}</button>
                <button class="button" onclick="event.preventDefault();document.getElementById('logout-form').submit();">{{ trans('consent.disagree_logout') }}</button>
            </div>
        </form>

    </div>
    
@stop
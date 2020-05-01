@extends('global')

@section('header')
    @include('headers.header', [
        'hide_menu' => true,
        'current_user_home_route' => $skip_to,
        'profile_url' => false,
    ])

@overwrite

@section('content')

    <div class="h-5"></div>

	<div class="max-w-4xl">
	

        <form action="{{ route('consent.dialog.statistic.update') }}" method="post" class="">

            {{ csrf_field() }}
            {{ method_field('PUT') }}

            <h2>{{ trans('consent.others.dialog_title') }}</h2>
            <p>{{ trans('consent.others.dialog_description') }}</p>

            <div class="h-5"></div>

            <div class=" mb-4">
                
                <strong>{{trans('consent.statistics.dialog_title')}}</strong>
                <p>{{trans('consent.statistics.dialog_description')}}</p>
                @if( $errors->has('statistics') )
                <span class="field-error">{{ implode(",", $errors->get('statistics'))  }}</span>
                @endif
                
                
                <input type="hidden" name="statistics" id="statistics" value="1">
                

            </div>
            

            <div class=" mb-4">
                <button class="button" type="submit">{{ trans('consent.statistics.agree_label') }}</button>
                <a class="button button--primary" href="{{$skip_to}}">{{ trans('consent.skip') }}</a>
            </div>
        </form>

    </div>
    
@stop
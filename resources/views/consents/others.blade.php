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
	

        <form action="{{ route('consent.dialog.others.update') }}" method="post" class="c-form">

            {{ csrf_field() }}
            {{ method_field('PUT') }}

            <h2>{{ trans('consent.others.dialog_title') }}</h2>
            <p>{{ trans('consent.others.dialog_description') }}</p>

            <div class="c-form__field">

                <label>{{trans('consent.notification.dialog_title')}}</label>
                <span class="description">{{trans('consent.notification.dialog_description')}}</span>
                @if( $errors->has('notifications') )
                    <span class="field-error">{{ implode(",", $errors->get('notifications'))  }}</span>
                @endif

                <p>
                    <input type="checkbox" name="notifications" id="notifications" value="1">&nbsp;<label for="notifications">{{ trans('consent.notification.agree_label') }}</label>
                </p>
                
            </div>

            <div class="c-form__field c-section--top-separated">

                
                <label>{{trans('consent.statistics.dialog_title')}}</label>
                <span class="description">{{trans('consent.statistics.dialog_description')}}</span>
                @if( $errors->has('statistics') )
                <span class="field-error">{{ implode(",", $errors->get('statistics'))  }}</span>
                @endif
                
                <p>
                    <input type="checkbox" name="statistics" id="statistics" value="1">&nbsp;<label for="statistics">{{ trans('consent.statistics.agree_label') }}</label>
                </p>

            </div>
            

            <div class="c-form__field">
                <button class="button button--primary" type="submit">{{ trans('consent.save') }}</button>
                <a href="$skip_to">{{ trans('consent.skip') }}</a>
            </div>
        </form>

    </div>
    
@stop
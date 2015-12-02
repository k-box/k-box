@extends('widgets.widget-layout')

@section('widget_class')
widget-sessions
@overwrite

@section('widget_title')
<span class="widget-icon icon-action-black icon-action-black-ic_account_circle_black_24dp"></span> {{trans('widgets.user_sessions.title')}}
@overwrite

@section('widget_content')
	@forelse ($active_users as $session)

		<div>

			@include('avatar.picture', ['image' => null, 'inline' => true, 'user_name' => $session['user'], 'no_link' => true])
			
			<span>
				<strong>{{$session['user']}}</strong> {{$session['time']}} 
			</span>
		</div>

	@empty

		<p>{{trans('widgets.user_sessions.empty')}}</p>

	@endforelse

@overwrite
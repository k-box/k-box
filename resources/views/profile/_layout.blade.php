
@extends('global')

@section('breadcrumbs')
        
    {{trans('profile.page_title', ['name' => $current_user_name])}}

@stop


@section('content')

	<div class="c-column c-column--short">

		<div class="navigation navigation--secondary">
		
			<a href="{{ route('profile.index') }}" class="navigation__item navigation__item--link">
				@component('avatar.full', ['image' => auth()->user()->avatar, 'name' => $current_user_name])
					{{$current_user_name}}
				@endcomponent
			</a>
			<a href="{{ route('profile.index') }}" class="navigation__item navigation__item--link @if(request()->is('*profile')) navigation__item--current @endif">
				
				@materialicon('action', 'account_circle', 'navigation__item__icon')
				
				{{trans('profile.profile')}}
			</a>
			<a href="{{ route('profile.privacy.index') }}" class="navigation__item navigation__item--link @if(request()->is('*profile/privacy')) navigation__item--current @endif">
				
				@materialicon('hardware', 'security', 'navigation__item__icon')
				
				{{trans('profile.privacy.privacy')}}
			</a>
			<a href="{{ route('profile.email.index') }}" class="navigation__item navigation__item--link @if(request()->is('*profile/email')) navigation__item--current @endif">
				
				@materialicon('communication', 'mail_outline', 'navigation__item__icon')
				
				{{trans('profile.email_section')}}
			</a>
			<a href="{{ route('profile.password.index') }}" class="navigation__item navigation__item--link @if(request()->is('*profile/password')) navigation__item--current @endif">
				
				@materialicon('action', 'lock', 'navigation__item__icon')
				
				{{trans('profile.password_section')}}
			</a>

		</div>
		


	</div>

	<div class="c-column c-column--medium">


			@include('errors.list')

			@if(Session::has('flash_message'))

				<div class="c-message c-message--success">
					{{session('flash_message')}}
				</div>

			@endif

			@yield('profile_page')

	</div>

		


@stop

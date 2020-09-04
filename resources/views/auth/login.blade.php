@extends('layout.hero')

@section('content')

	<form action="{{ route('login') }}" class="" method="POST">

		<h2 class="mb-4 text-2xl font-normal">{{ $welcome_string }}</h2>

		@if (\KBox\Auth\Registration::isEnabled() && ! \KBox\Auth\Registration::requiresInvite())
			<div class="mb-4">
				{{ trans('auth.no_account') }}&nbsp;<a tabindex="4" class="" href="{{ route('register') }}">{{ trans('auth.register') }}</a>
			</div>
		@endif

        <div class="mt-6">

			<x-oneofftech-identity-link action="login" provider="gitlab"  class="button button--primary"/>

			@error('gitlab')
                <span class="field-error" role="alert">
                    {{ $message }}
                </span>
			@enderror
		</div>
		
		<div class="h-5"></div>

		<div class=" mb-4">
			<label for="email">{{trans('auth.email_label')}}</label>
			@if( isset($errors) && $errors->has('email') )
				<span class="field-error">{{ implode(",", isset($errors) && $errors->get('email') ? $errors->get('email') : [])  }}</span>
			@endif
			<input type="email" class="form-input block w-full sm:mx-auto lg:mx-0 sm:w-2/4 lg:w-2/3" required id="email" name="email" tabindex="1" value="@if(isset($email)){{$email}}@endif" />
		</div>

		<div class=" mb-4">
			<label for="password">{{trans('auth.password_label')}} </label>
			@if( isset($errors) && $errors->has('password') )
				<span class="field-error">{{ implode(",", isset($errors) && $errors->get('password') ? $errors->get('password') : [])  }}</span>
			@endif
			<input type="password" class="form-input block w-full sm:mx-auto lg:mx-0 sm:w-2/4 lg:w-2/3" required name="password" tabindex="2" id="password" />

			@if (Route::has('password.request'))
				<a  tabindex="4" class="" href="{{ route('password.request') }}">
					{{trans('passwords.forgot.link')}}
				</a>
			@endif
		</div>

		<div class="mb-4">
			@component('components.checkbox', ['name' => 'remember', 'checked' => old('remember') ? true : false])
				{{ trans('auth.remember_me') }}
			@endcomponent
		</div>

		{{ csrf_field() }}

		<div class="">
			<input type="submit" id="login-submit" name="login-submit" class="button button--primary w-32"  tabindex="3" value="{{trans('auth.login')}}">
		</div>
			
	</form>
@endsection

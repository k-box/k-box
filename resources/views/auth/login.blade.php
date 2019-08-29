@extends('layout.login')


@section('form')

	<form action="{{ route('login') }}" class="c-form c-form--space" method="POST">
		
		<h2 class="mb-1">{{ $welcome_string }}</h2>

		@if (Route::has('register'))
			<div class="mb-4">
				{{ trans('auth.no_account') }}&nbsp;<a  tabindex="4" class="" href="{{ route('register') }}">
					{{ trans('auth.register') }}
				</a>
			</div>
		@endif

		<div class=" mb-4">
			<label for="email">{{trans('auth.email_label')}}</label>
			@if( isset($errors) && $errors->has('email') )
				<span class="field-error">{{ implode(",", isset($errors) && $errors->get('email') ? $errors->get('email') : [])  }}</span>
			@endif
			<input type="email" class="form-input block w-2/3" required id="email" name="email" tabindex="1" value="@if(isset($email)){{$email}}@endif" />
		</div>

		<div class=" mb-4  mb-4">
			<label for="password">{{trans('auth.password_label')}} </label>
			@if( isset($errors) && $errors->has('password') )
				<span class="field-error">{{ implode(",", isset($errors) && $errors->get('password') ? $errors->get('password') : [])  }}</span>
			@endif
			<input type="password" class="form-input block w-2/3" required name="password" tabindex="2" id="password" />

			@if (Route::has('password.request'))
				<a  tabindex="4" class="" href="{{ route('password.request') }}">
					{{trans('passwords.forgot.link')}}
				</a>
			@endif
		</div>

		

		{{ csrf_field() }}

		<div class="c-form__buttons">
			<input type="submit" id="login-submit" name="login-submit" class="button button--primary"  tabindex="3" value="{{trans('auth.login')}}">
			
			<label style="display:inline-block;margin-left:16px">
				<input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ trans('auth.remember_me') }}
			</label>
		</div>
			
			
			
		
	</form>
@stop
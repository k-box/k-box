@extends('layout.login')

@push('title')
    @lang('passwords.forgot.title') &ndash; 
@endpush

@section('form')

	@if(Session::has('status'))

		<div class="alert success">
			{{session('status')}}
		</div>

	@endif

	<form method="post" class="c-form c-form--space" action="{{ route('password.email') }}">

		<h2 class="mb-1">{{trans('passwords.forgot.title')}}</h2>
		
		<p class="mb-4">{{trans('passwords.forgot.instructions')}}</p>

		{{ csrf_field() }}
		
		<div class="c-form__field mb-4">
			<label for="email">{{trans('auth.email_label')}}</label>
			@if( isset($errors) && $errors->has('email') )
				<span class="field-error">{{ implode(",", isset($errors) && $errors->get('email') ? $errors->get('email') : [])  }}</span>
			@endif
			<input type="email" class="c-form__input c-form__input--larger" required id="email" tabindex="1" name="email" value="" autofocus />
		</div>

		<div class="c-form__field mb-8">
			<button type="submit" class="button button--primary" tabindex="2">{{trans('passwords.forgot.submit')}}</button>
		</div>

		<div class="mb-4">
			{{ trans('auth.have_login_and_password') }}&nbsp;<a tabindex="3" class="" href="{{ route('register') }}">
				{{ trans('auth.login') }}
			</a>
		</div>

	</form>

@stop

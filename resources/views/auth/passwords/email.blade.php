@extends('layout.login')

@push('title')
    @lang('passwords.forgot.title') &ndash; 
@endpush

@section('form')

	@if(Session::has('status'))

		<div class="alert info">
			{{session('status')}}
		</div>
		
	@elseif( isset($errors) && $errors->has('email') )
		
		<div class="alert info">
			{{ trans('passwords.sent') }}
		</div>
		
	@endif

	<form method="post" class="c-form c-form--space" action="{{ route('password.email') }}">

		<h2 class="mb-1">{{trans('passwords.forgot.title')}}</h2>
		
		<p class="mb-4">{{trans('passwords.forgot.instructions')}}</p>

		{{ csrf_field() }}
		
		<div class=" mb-4 mb-4">
			<label for="email">{{trans('auth.email_label')}}</label>
			
			<input type="email" class="form-input block w-full sm:w-2/3" required id="email" tabindex="1" name="email" value="" autofocus />
		</div>

		<div class=" mb-4 mb-8">
			<button type="submit" class="button button--primary" tabindex="2">{{trans('passwords.forgot.submit')}}</button>
		</div>

		<div class="mb-4">
			{{ trans('auth.have_login_and_password') }}&nbsp;<a tabindex="3" class="" href="{{ route('login') }}">
				{{ trans('auth.login') }}
			</a>
		</div>

	</form>

@stop

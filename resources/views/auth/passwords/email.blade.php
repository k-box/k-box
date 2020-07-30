@extends('layout.full-form')

@push('title')
    @lang('passwords.forgot.title') &ndash; 
@endpush

@section('content')

	@if(Session::has('status'))

		<div class="c-message c-message--success">
			{{session('status')}}
		</div>
		
	@elseif( isset($errors) && $errors->has('email') )
		
		<div class="c-message c-message--success">
			{{ trans('passwords.sent') }}
		</div>
		
	@endif

	<form method="post" class="" action="{{ route('password.email') }}">

		<h2 class="mb-1">{{trans('passwords.forgot.title')}}</h2>
		
		<p class="mb-4">{{trans('passwords.forgot.instructions')}}</p>

		{{ csrf_field() }}
		
		<div class=" mb-4">
			<label for="email">{{trans('auth.email_label')}}</label>
			
			<input type="email" class="form-input block w-full sm:mx-auto lg:mx-0 sm:w-2/4 lg:w-2/3" required id="email" tabindex="1" name="email" value="" autofocus />
		</div>

		<div class=" mb-8">
			<button type="submit" class="button button--primary" tabindex="2">{{trans('passwords.forgot.submit')}}</button>
		</div>

		<div class="mb-4">
			{{ trans('auth.have_login_and_password') }}
			<a tabindex="3" class="" href="{{ route('login') }}">{{ trans('auth.login') }}
			</a>
		</div>

	</form>

@stop

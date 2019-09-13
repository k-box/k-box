@extends('layout.login')


@push('title')
    @lang('passwords.reset.title') &ndash; 
@endpush

@section('form')

	<form method="post" class="c-form c-form--space" action="{{ route('password.update') }}">

		<h2 class="mb-1">{{trans('passwords.reset.title')}}</h2>

		<p class="mb-4">{{trans('passwords.reset.instructions')}}</p>

		{{ csrf_field() }}

		<input type="hidden" name="token" value="{{ $token }}"> 

		<div class=" mb-4 mb-4">
			<label for="email">{{trans('auth.email_label')}}</label>
			@if( isset($errors) && $errors->has('email') )
				<span class="field-error">{{ implode(",", isset($errors) && $errors->get('email') ? $errors->get('email') : [])  }}</span>
			@endif
			<input type="email" class="form-input block w-full sm:w-2/3" required id="email" name="email" value="{{old('email')}}" />
		</div>

		<div class=" mb-4 mb-4">
						
			<label for="password">{{trans('profile.labels.password')}}</label>
			<p class="description">{{trans('profile.labels.password_description')}}</p>
			@if( $errors->has('password') )
				<span class="field-error">{{ implode(",", $errors->get('password') ? $errors->get('password') : [])  }}</span>
			@endif
			<input type="password" class="form-input block w-full sm:w-2/3" name="password" />
		</div>

		<div class=" mb-4 mb-4">
			
			<label for="password_confirmation">{{trans('profile.labels.password_confirm')}}</label>
			@if( $errors->has('password_confirm') )
				<span class="field-error">{{ implode(",", $errors->get('password_confirm'))  }}</span>
			@endif
			<input type="password" class="form-input block w-full sm:w-2/3" name="password_confirmation" />
		</div>

		<div class=" mb-4 mb-4">
			<button type="submit" class="button button--primary">{{trans('passwords.reset.submit')}}</button>
		</div>

	</form>

@stop
@extends('layout.hero')

@section('content')

	<form action="{{ route('login') }}" class="" method="POST">

		<h2 class="mb-4 text-2xl font-normal">{{ $welcome_string }}</h2>

        @if(\KBox\Facades\Identity::isEnabled())
            <div class=" mb-4">

                @foreach (\KBox\Facades\Identity::enabledProviders() as $provider)
					<x-oneofftech-identity-link action="login" :provider="$provider" class="button button--primary"/>

					@error($provider)
						<div class="field-error block mt-2" role="alert">
							{{ $message }}
							
							@if ($message === trans('auth.not_found') && \KBox\Auth\Registration::isEnabled() && ! \KBox\Auth\Registration::requiresInvite())
								<p>
									<a class="text-white underline hover:text-white focus:text-white" href="{{ route('register') }}">{{ trans('auth.create_account') }}</a>
								</p>
							@endif
						</div>
					@enderror
		        @endforeach
            </div>
			<div class="flex mb-4 flex-nowrap items-center max-w-lg mx-auto lg:mx-0">
				<div class="h-px w-1 lg:w-12 flex-grow lg:flex-grow-0 bg-gray-200"></div>
				<p class="ml-2 mr-2 text-gray-600 font-medium">{{ trans('auth.or') }}</p>
				<div class="h-px w-1 flex-grow bg-gray-200"></div>
			</div>
        @endif


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

	@if (\KBox\Auth\Registration::isEnabled() && ! \KBox\Auth\Registration::requiresInvite())
		<div class="flex py-4 flex-nowrap max-w-lg mx-auto lg:mx-0">
			<div class="h-px w-1 flex-grow bg-gray-200"></div>
		</div>

		<div class="">
			{{ trans('auth.no_account') }}&nbsp;<a tabindex="4" class="" href="{{ route('register') }}">{{ trans('auth.register') }}</a>
		</div>
	@endif
@endsection

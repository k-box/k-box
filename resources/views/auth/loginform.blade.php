
<form action="{{ url('/login') }}" class="c-form c-form--space" method="POST">
	
	<h2 class="">{{ $welcome_string }}</h2>

	<div class="c-form__field">
		<label for="email">{{trans('login.form.email_label')}}</label>
		@if( isset($errors) && $errors->has('email') )
			<span class="field-error">{{ implode(",", isset($errors) && $errors->get('email') ? $errors->get('email') : [])  }}</span>
		@endif
		<input type="email" class="c-form__input c-form__input--larger" required id="email" name="email" tabindex="1" placeholder="{{trans('login.form.email_placeholder')}}" value="@if(isset($email)){{$email}}@endif" />
	</div>

	<div class="c-form__field">
		<label for="password">{{trans('login.form.password_label')}} </label>
		@if( isset($errors) && $errors->has('password') )
			<span class="field-error">{{ implode(",", isset($errors) && $errors->get('password') ? $errors->get('password') : [])  }}</span>
		@endif
		<input type="password" class="c-form__input c-form__input--larger" required name="password" tabindex="2" id="password" placeholder="{{trans('login.form.password_placeholder')}}" />
		<a href="{{ url('/password/reset') }}" tabindex="4" class="white">({{trans('passwords.forgot.link')}})</a>
	</div>

	

	@csrf

	<div class="c-form__buttons">
		<input type="submit" id="login-submit" name="login-submit" class="button"  tabindex="3" value="{{trans('login.form.submit')}}">

		<label style="display:inline-block;margin-left:16px">
			<input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ trans('login.form.remember_me') }}
		</label>
	</div>

</form>
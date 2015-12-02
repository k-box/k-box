
<form action="{{ url('auth/login') }}" class="login-form" method="POST">
	
	<p>
		<label for="email">{{trans('login.form.email_label')}}</label>
		@if( $errors->has('email') )
			<span class="field-error">{{ implode(",", $errors->get('email'))  }}</span>
		@endif
		<input type="email" required id="email" name="email" tabindex="1" placeholder="{{trans('login.form.email_placeholder')}}" value="@if(isset($email)){{$email}}@endif" />
	</p>

	<p>
		<label for="password">{{trans('login.form.password_label')}} <a href="{{ action('Auth\PasswordController@getEmail') }}"  tabindex="4" class="forgot-link">({{trans('passwords.forgot.link')}})</a></label>
		@if( $errors->has('password') )
			<span class="field-error">{{ implode(",", $errors->get('password'))  }}</span>
		@endif
		<input type="password" required name="password" tabindex="2" id="password" placeholder="{{trans('login.form.password_placeholder')}}" />


	</p>

	<input type="hidden" name="_token" value="{{{ csrf_token() }}}" />

	<p>
		<input type="submit" class="button-primary"  tabindex="3" value="{{trans('login.form.submit')}}">
	</p>

</form>
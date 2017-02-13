@extends('management-layout')

@section('sub-header')

	{{trans('passwords.reset.title')}}

@stop

@section('content')

<div class="row">

	<div class="twelve columns">



	<form method="post" action="{{action('Auth\PasswordController@postReset')}}">

	   <input type="hidden" name="_token" value="{{{ csrf_token() }}}"> 

	   <input type="hidden" name="token" value="{{ $token }}"> 

	   <p>{{trans('passwords.reset.instructions')}}</p>

	   <p>
			<label for="email">{{trans('login.form.email_label')}}</label>
			@if( isset($errors) && $errors->has('email') )
				<span class="field-error">{{ implode(",", isset($errors) && $errors->get('email'))  }}</span>
			@endif
			<input type="email" required id="email" name="email" placeholder="{{trans('login.form.email_placeholder')}}" value="{{old('email')}}" />
		</p>

				

	   <p>
				        
	        <label>{{trans('profile.labels.password')}}</label>
	        @if( $errors->has('password') )
	            <span class="field-error">{{ implode(",", $errors->get('password'))  }}</span>
	        @endif
	        <input type="password" name="password" />
	        <p class="description">{{trans('profile.labels.password_description')}}</p>
	    </p>

	    <p>
	        
	        <label>{{trans('profile.labels.password_confirm')}}</label>
	        @if( $errors->has('password_confirm') )
	            <span class="field-error">{{ implode(",", $errors->get('password_confirm'))  }}</span>
	        @endif
	        <input type="password" name="password_confirmation" />
	    </p>

	    <p>
			<button type="submit" class="button-primary">{{trans('passwords.reset.submit')}}</button>
		</p>

	</form>



	


	</div>
	

</div>

@stop
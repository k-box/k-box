@extends('management-layout')

@section('sub-header')

	{{trans('passwords.forgot.title')}}

@stop



@section('content')

<div class="row">

	<div class="twelve columns">

		@if(Session::has('status'))

			<div class="alert success">
				{{session('status')}}
			</div>

		@endif


		<form method="post" action="{{action('Auth\PasswordController@postEmail')}}">

		   <input type="hidden" name="_token" value="{{{ csrf_token() }}}"> 

		   <p>{{trans('passwords.forgot.instructions')}}</p>

		   <p>
				<label for="email">{{trans('login.form.email_label')}}</label>
				@if( isset($errors) && $errors->has('email') )
					<span class="field-error">{{ implode(",", isset($errors) && $errors->get('email'))  }}</span>
				@endif
				<input type="email" required id="email" name="email" placeholder="{{trans('login.form.email_placeholder')}}" value="" />
			</p>

			<p>
				<button type="submit" class="button-primary">{{trans('passwords.forgot.submit')}}</button>
			</p>

		</form>
		

	</div>
	

</div>

@stop

@extends('global')

@section('breadcrumbs')

	{{trans('passwords.reset.title')}}

@stop

@push('title')
    @lang('passwords.reset.title') &ndash; 
@endpush

@section('content')

<div class="row">

	<div class="twelve columns">



	<form method="post" action="{{ url('/password/reset') }}">

	   {{ csrf_field() }}

	   <input type="hidden" name="token" value="{{ $token }}"> 

	   <p>{{trans('passwords.reset.instructions')}}</p>

	   <p>
			<label for="email">{{trans('auth.email_label')}}</label>
			@if( isset($errors) && $errors->has('email') )
				<span class="field-error">{{ implode(",", isset($errors) && $errors->get('email') ? $errors->get('email') : [])  }}</span>
			@endif
			<input type="email" required id="email" name="email" value="{{old('email')}}" />
		</p>

				

	   <p>
				        
	        <label>{{trans('profile.labels.password')}}</label>
	        @if( $errors->has('password') )
	            <span class="field-error">{{ implode(",", $errors->get('password') ? $errors->get('password') : [])  }}</span>
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
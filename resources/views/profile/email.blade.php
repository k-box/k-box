
@extends('profile._layout')


@section('profile_page')

	<form method="post"  class="" action="{{route('profile.email.update')}}">
		
		{{ csrf_field() }}
		{{ method_field('put') }}
		<input type="hidden" name="_change" value="mail">

		<h4>{{trans('profile.email_section')}}</h4>

		<div class=" mb-4">
			
			<label>{{trans('administration.accounts.labels.email')}}</label>
			@if( $errors->has('email') )
				<span class="field-error">{{ implode(",", $errors->get('email'))  }}</span>
			@endif
			<input type="text"  class="form-input block" name="email" value="{{ old('email', $user->email) }}" @if(isset($can_change_mail) && !$can_change_mail) disabled @endif />
		</div>
		
		
		<div class=" mb-4">
			
			<button type="submit" class="button">{{trans('profile.change_mail_btn')}}</button>
		</div>


	</form>

@stop

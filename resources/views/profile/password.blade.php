
@extends('profile._layout')


@section('profile_page')



	<form method="post"  class="" action="{{route('profile.password.update')}}">
		
		{{ csrf_field() }}
		{{ method_field('PUT') }}

		<h4>{{trans('profile.password_section')}}</h4>

		<div class=" mb-4">
			
			<label>{{trans('profile.labels.password')}}</label>
			@if( $errors->has('password') )
				<span class="field-error">{{ implode(",", $errors->get('password'))  }}</span>
			@endif
			<input type="password" class="form-input block" name="password" />
			<p class="description">{{trans('profile.labels.password_description')}}</p>
		</div>

		<div class=" mb-4">
			
			<label>{{trans('profile.labels.password_confirm')}}</label>
			@if( $errors->has('password_confirm') )
				<span class="field-error">{{ implode(",", $errors->get('password_confirm'))  }}</span>
			@endif
			<input type="password" class="form-input block" name="password_confirm" />
		</div>
		
		
		<div class=" mb-4">
			
			<button type="submit" class="button">{{trans('profile.change_password_btn')}}</button>
		</div>


	</form>

@stop

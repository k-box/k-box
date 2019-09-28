
@extends('profile._layout')


@section('profile_page')

	<h4 class="my-4">{{trans('invite.create.title')}}</h4>

	<div class="mb-8">
		
		<form action="{{ route('profile.invite.store') }}" method="post">

			{{ csrf_field() }}

			<div class=" mb-4">
				<label>{{trans('administration.accounts.labels.email')}}</label>
				@if( $errors->has('email') )
					<span class="field-error">{{ implode(",", $errors->get('email'))  }}</span>
				@endif
				<input class="form-input block" type="email" name="email" value="{{ old('email') }}" />
			</div>

			<div class="c-form__buttons">
				<button type="submit" class="button button--primary">{{ trans('invite.create.btn') }}</button>
			</div>
		</form>
	
	</div>

@endsection

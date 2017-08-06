
@extends('global')

@section('breadcrumbs')
        
    {{trans('profile.page_title', ['name' => $current_user_name])}}

@stop


@section('content')

	<div class="c-column c-column--short">
		
		<p>&nbsp;</p>

		@component('avatar.full', ['image' => $user->avatar, 'name' => $user->name])

			{{$user->name}}

		@endcomponent

		

		<p>

			<div class="iconized">

				@materialicon('toggle', 'star') {{trans_choice('profile.starred_count_label', $stars_count, ['number' => $stars_count])}}

			</div>

		</p>

		<p>
			<div class="iconized">

				@materialicon('action','description') {{trans_choice('profile.documents_count_label', $documents_count, ['number' => $documents_count])}}

			</div>
		</p>

		<p>
			<div class="iconized">

				@materialicon('action','label') {{trans_choice('profile.collections_count_label', $collections_count, ['number' => $collections_count])}}

			</div>
		</p>

		<p>
			<div class="iconized">

				@materialicon('social','people') {{trans_choice('profile.shared_count_label', $shares_count, ['number' => $shares_count])}}

			</div>
		</p>

	</div>

	<div class="c-column c-column--medium">


			@include('errors.list')

			
			<form method="post" class="c-form" action="{{route('profile.store')}}">
				
				{{ csrf_field() }}
				<input type="hidden" name="_change" value="info">

				<h4>{{trans('profile.info_section')}}</h4>

			    <div class="c-form__field">
			        
			        <label>{{trans('profile.labels.nicename')}}</label>
			        @if( $errors->has('name') )
			            <span class="field-error">{{ implode(",", $errors->get('name'))  }}</span>
			        @endif
			        <input type="text"  class="c-form__input" name="name" @if(isset($user)) value="{{$user->name}}" @endif />
			    </div>
			    
			    
			    <div class="c-form__field">
			        
			        <button type="submit" class="button">{{trans('profile.update_profile_btn')}}</button>
			    </div>


			</form>

			<form method="post"  class="c-form" action="{{route('profile.store')}}">
				
				{{ csrf_field() }}
				<input type="hidden" name="_change" value="language">

				<h4>{{trans('profile.language_section')}}</h4>

			    <div class="c-form__field">
			        
			        <label>{{trans('profile.labels.language')}}</label>
			        @if( $errors->has('language') )
			            <span class="field-error">{{ implode(",", $errors->get('language'))  }}</span>
			        @endif
					
					<select class="c-form__input" name="language">
						<option value="en" @if($language=='en') selected @endif>{{trans('languages.en')}}</option>
						<option value="ru" @if($language=='ru') selected @endif>{{trans('languages.ru')}}</option>
					</select>
			    </div>
			    
			    
			    <div class="c-form__field">
			        
			        <button type="submit" class="button">{{trans('profile.change_language_btn')}}</button>
			    </div>


			</form>

			<form method="post"  class="c-form" action="{{route('profile.store')}}">
				
				{{ csrf_field() }}
				<input type="hidden" name="_change" value="mail">

				<h4>{{trans('profile.email_section')}}</h4>

			    <div class="c-form__field">
			        
			        <label>{{trans('administration.accounts.labels.email')}}</label>
			        @if( $errors->has('email') )
			            <span class="field-error">{{ implode(",", $errors->get('email'))  }}</span>
			        @endif
			        <input type="text"  class="c-form__input" name="email" @if(isset($user)) value="{{$user->email}}" @endif @if(isset($can_change_mail) && !$can_change_mail) disabled @endif />
			    </div>
			    
			    
			    <div class="c-form__field">
			        
			        <button type="submit" class="button">{{trans('profile.change_mail_btn')}}</button>
			    </div>


			</form>


			<form method="post"  class="c-form" action="{{route('profile.store')}}">
				
				{{ csrf_field() }}
				<input type="hidden" name="_change" value="pass">

				<h4>{{trans('profile.password_section')}}</h4>

			    <div class="c-form__field">
			        
			        <label>{{trans('profile.labels.password')}}</label>
			        @if( $errors->has('password') )
			            <span class="field-error">{{ implode(",", $errors->get('password'))  }}</span>
			        @endif
			        <input type="password" class="c-form__input" name="password" />
			        <p class="description">{{trans('profile.labels.password_description')}}</p>
			    </div>

			    <div class="c-form__field">
			        
			        <label>{{trans('profile.labels.password_confirm')}}</label>
			        @if( $errors->has('password_confirm') )
			            <span class="field-error">{{ implode(",", $errors->get('password_confirm'))  }}</span>
			        @endif
			        <input type="password" class="c-form__input" name="password_confirm" />
			    </div>
			    
			    
			    <div class="c-form__field">
			        
			        <button type="submit" class="button">{{trans('profile.change_password_btn')}}</button>
			    </div>


			</form>

	</div>

		


@stop

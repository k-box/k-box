
@extends('management-layout')

@section('sub-header')
        
    {{trans('profile.page_title', ['name' => $current_user_name])}}

@stop


@section('content')

	<div class="row">

		<div class="three columns">
			
			<p>
				@include('avatar.picture', ['image' => $user->avatar])
			</p>

			<p>

				<div class="iconized">

					<span class="icon-toggle-black icon-toggle-black-ic_star_black_24dp"></span>{{trans_choice('profile.starred_count_label', $stars_count, ['number' => $stars_count])}}

				</div>

			</p>

			<p>
				<div class="iconized">

					<span class="icon-action-black icon-action-black-ic_description_black_24dp"></span>{{trans_choice('profile.documents_count_label', $documents_count, ['number' => $documents_count])}}

				</div>
			</p>

			<p>
				<div class="iconized">

					<span class="icon-file-black icon-file-black-ic_folder_black_24dp"></span>{{trans_choice('profile.collections_count_label', $collections_count, ['number' => $collections_count])}}

				</div>
			</p>

			<p>
				<div class="iconized">

					<span class="icon-social-black icon-social-black-ic_people_black_24dp"></span>{{trans_choice('profile.shared_count_label', $shares_count, ['number' => $shares_count])}}

				</div>
			</p>

		</div>

		<div class="nine columns">


			@include('errors.list')

			
			<form method="post" action="{{route('profile.store')}}" class="widget">
				
				<input type="hidden" name="_token" value="{{{ csrf_token() }}}"> 
				<input type="hidden" name="_change" value="info">

				<h5>{{trans('profile.info_section')}}</h5>

			    <p>
			        
			        <label>{{trans('profile.labels.nicename')}}</label>
			        @if( $errors->has('name') )
			            <span class="field-error">{{ implode(",", $errors->get('name'))  }}</span>
			        @endif
			        <input type="text" name="name" @if(isset($user)) value="{{$user->name}}" @endif />
			    </p>
			    
			    
			    <p>
			        
			        <button type="submit">{{trans('profile.update_profile_btn')}}</button>
			    </p>


			</form>

			<form method="post" action="{{route('profile.store')}}" class="widget">
				
				<input type="hidden" name="_token" value="{{{ csrf_token() }}}"> 
				<input type="hidden" name="_change" value="language">

				<h5>{{trans('profile.language_section')}}</h5>

			    <p>
			        
			        <label>{{trans('profile.labels.language')}}</label>
			        @if( $errors->has('language') )
			            <span class="field-error">{{ implode(",", $errors->get('language'))  }}</span>
			        @endif
					
					<select name="language">
						<option value="en" @if($language=='en') selected @endif>{{trans('languages.en')}}</option>
						<option value="ru" @if($language=='ru') selected @endif>{{trans('languages.ru')}}</option>
					</select>
			    </p>
			    
			    
			    <p>
			        
			        <button type="submit">{{trans('profile.change_language_btn')}}</button>
			    </p>


			</form>

			<form method="post" action="{{route('profile.store')}}" class="widget">
				
				<input type="hidden" name="_token" value="{{{ csrf_token() }}}"> 
				<input type="hidden" name="_change" value="mail">

				<h5>{{trans('profile.email_section')}}</h5>

			    <p>
			        
			        <label>{{trans('administration.accounts.labels.email')}}</label>
			        @if( $errors->has('email') )
			            <span class="field-error">{{ implode(",", $errors->get('email'))  }}</span>
			        @endif
			        <input type="text" name="email" @if(isset($user)) value="{{$user->email}}" @endif @if(isset($can_change_mail) && !$can_change_mail) disabled @endif />
			    </p>
			    
			    
			    <p>
			        
			        <button type="submit">{{trans('profile.change_mail_btn')}}</button>
			    </p>


			</form>


			<form method="post" action="{{route('profile.store')}}" class="widget">
				
				<input type="hidden" name="_token" value="{{{ csrf_token() }}}">
				<input type="hidden" name="_change" value="pass">

				<h5>{{trans('profile.password_section')}}</h5>

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
			        <input type="password" name="password_confirm" />
			    </p>
			    
			    
			    <p>
			        
			        <button type="submit">{{trans('profile.change_password_btn')}}</button>
			    </p>


			</form>

		</div>
		
	</div>

		


@stop

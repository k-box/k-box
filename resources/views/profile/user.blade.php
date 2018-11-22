
@extends('profile._layout')


@section('profile_page')

	<div>
		<div class="iconized box box--inline">

			@materialicon('toggle', 'star') {{trans_choice('profile.starred_count_label', $stars_count, ['number' => $stars_count])}}

		</div>
		<div class="iconized box box--inline">

			@materialicon('action','description') {{trans_choice('profile.documents_count_label', $documents_count, ['number' => $documents_count])}}

		</div>
		<div class="iconized box box--inline">

			@materialicon('action','label') {{trans_choice('profile.collections_count_label', $collections_count, ['number' => $collections_count])}}

		</div>
		<div class="iconized box box--inline">

			@materialicon('social','people') {{trans_choice('profile.shared_count_label', $shares_count, ['number' => $shares_count])}}

		</div>
	</div>

	<form method="post" class="c-form" action="{{route('profile.update')}}">
		
		{{ csrf_field() }}
		{{ method_field('PUT') }}
		<input type="hidden" name="_change" value="info">

		<h4>{{trans('profile.info_section')}}</h4>

		<div class="c-form__field">
			<label>{{trans('profile.labels.nicename')}}</label>
			<span class="description">{{trans('profile.labels.nicename_hint')}}</span>
			@if( $errors->has('name') )
				<span class="field-error">{{ implode(",", $errors->get('name'))  }}</span>
			@endif
			<input type="text"  class="c-form__input" name="name" @if(isset($user)) value="{{$user->name}}" @endif />
		</div>
		
		<div class="c-form__field">			        
			<label>{{trans('profile.labels.organization_name')}}</label>
			<span class="description">{{trans('profile.labels.organization_name_hint')}}</span>
			@if( $errors->has('organization_name') )
				<span class="field-error">{{ implode(",", $errors->get('organization_name'))  }}</span>
			@endif
			<input type="text"  class="c-form__input" name="organization_name" value="{{ old('organization_name', $user->organization_name)}}" />
		</div>
		
		<div class="c-form__field">			        
			<label>{{trans('profile.labels.organization_website')}}</label>
			<span class="description">{{trans('profile.labels.organization_website_hint')}}</span>
			@if( $errors->has('organization_website') )
				<span class="field-error">{{ implode(",", $errors->get('organization_website'))  }}</span>
			@endif
			<input type="text" placeholder="http://" class="c-form__input" name="organization_website" value="{{ old('organization_website', $user->organization_website)}}" />
		</div>
		
		
		<div class="c-form__field">
			
			<button type="submit" class="button">{{trans('profile.update_profile_btn')}}</button>
		</div>


	</form>

	<form method="post"  class="c-form" action="{{route('profile.language.update')}}">
		
		{{ csrf_field() }}
		{{ method_field('PUT') }}

		<h4>{{trans('profile.language_section')}}</h4>

		<div class="c-form__field">
			
			<label>{{trans('profile.labels.language')}}</label>
			@if( $errors->has('language') )
				<span class="field-error">{{ implode(",", $errors->get('language'))  }}</span>
			@endif
			
			<select class="c-form__input" name="language">
				<option value="en" @if($language=='en') selected @endif>{{trans('languages.en')}}</option>
				<option value="ru" @if($language=='ru') selected @endif>{{trans('languages.ru')}}</option>
				<option value="tg" @if($language=='tg') selected @endif>{{trans('languages.tg')}}</option>
				<option value="fr" @if($language=='fr') selected @endif>{{trans('languages.fr')}}</option>
				<option value="de" @if($language=='de') selected @endif>{{trans('languages.de')}}</option>
				<option value="ky" @if($language=='ky') selected @endif>{{trans('languages.ky')}}</option>
			</select>
		</div>
		
		
		<div class="c-form__field">
			
			<button type="submit" class="button">{{trans('profile.change_language_btn')}}</button>
		</div>


	</form>

	

@stop

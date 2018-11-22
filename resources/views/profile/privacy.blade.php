
@extends('profile._layout')


@section('profile_page')

	<h4>{{trans('profile.privacy.section_name')}}</h4>
	<span class="description">{{trans('profile.privacy.section_description')}}</span>

	<div class="c-form__field">

		<div class="c-form__column">
			<button class="button button--ghost" onclick="event.preventDefault();document.getElementById('consent-notifications').submit();">
				@if($consent_notification_given)
					@materialicon('toggle', 'check_box')
				@else 
					@materialicon('toggle', 'check_box_outline_blank')
				@endif
			</button>
			<form id="consent-notifications" action="{{ route('profile.privacy.update') }}" method="POST" style="display: none;">
				{{ csrf_field() }}
				{{ method_field('PUT') }}
				<input type="hidden" name="notifications" value="{{ $consent_notification_given ? '0' : '1' }}">
			</form>
		</div>

		<label>{{trans('consent.notification.dialog_title')}}</label>
		<span class="description">{{trans('consent.notification.dialog_description')}}</span>
		@if( $errors->has('notifications') )
			<span class="field-error">{{ implode(",", $errors->get('notifications'))  }}</span>
		@endif
		
	</div>

	<div class="c-form__field c-section--top-separated">
		<div class="c-form__column">
			<button class="button button--ghost" onclick="event.preventDefault();document.getElementById('consent-statistics').submit();">
				@if($consent_statistics_given)
					@materialicon('toggle', 'check_box')
				@else 
					@materialicon('toggle', 'check_box_outline_blank')
				@endif
			</button>
			<form id="consent-statistics" action="{{ route('profile.privacy.update') }}" method="POST" style="display: none;">
				{{ csrf_field() }}
				{{ method_field('PUT') }}
				<input type="hidden" name="statistics" value="{{ $consent_statistics_given ? '0' : '1' }}">
			</form>
		</div>

		<label>{{trans('consent.statistics.dialog_title')}}</label>
		<span class="description">{{trans('consent.statistics.dialog_description')}}</span>
		@if( $errors->has('statistics') )
			<span class="field-error">{{ implode(",", $errors->get('statistics'))  }}</span>
		@endif
	</div>
	

@stop

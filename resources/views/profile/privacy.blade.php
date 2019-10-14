
@extends('profile._layout')


@section('profile_page')

	<h4 class=" mt-4">{{trans('profile.privacy.section_name')}}</h4>
	<span class="description block mb-4">{{trans('profile.privacy.section_description')}}</span>

	@flag('consent_notifications')
	<div class="flex items-center mb-8">

		<div class="mr-4">
			<button class="button" onclick="event.preventDefault();document.getElementById('consent-notifications').submit();">
				@if($consent_notification_given)
					{{ trans('plugins.actions.disable') }}
				@else 
					{{ trans('plugins.actions.enable') }}
				@endif
			</button>
			<form id="consent-notifications" action="{{ route('profile.privacy.update') }}" method="POST" style="display: none;">
				{{ csrf_field() }}
				{{ method_field('PUT') }}
				<input type="hidden" name="notifications" value="{{ $consent_notification_given ? '0' : '1' }}">
			</form>
		</div>

		<div>
			<label class="block font-bold">{{trans('consent.notification.dialog_title')}}</label>
			<span class="description text-gray-700">{{trans('consent.notification.dialog_description')}}</span>
			@if( $errors->has('notifications') )
				<span class="field-error">{{ implode(",", $errors->get('notifications'))  }}</span>
			@endif
		</div>

	</div>
	@endflag

	<div class="flex items-center mb-8">
		<div class="mr-4">
			<button class="button " onclick="event.preventDefault();document.getElementById('consent-statistics').submit();">
				@if($consent_statistics_given)
					{{ trans('plugins.actions.disable') }}
				@else 
					{{ trans('plugins.actions.enable') }}
				@endif
			</button>
			<form id="consent-statistics" action="{{ route('profile.privacy.update') }}" method="POST" style="display: none;">
				{{ csrf_field() }}
				{{ method_field('PUT') }}
				<input type="hidden" name="statistics" value="{{ $consent_statistics_given ? '0' : '1' }}">
			</form>
		</div>

		<div>
			<label class="block font-bold">{{trans('consent.statistics.dialog_title')}} @if($consent_statistics_given) <span class="text-sm font-normal rounded-full py-1 px-2 bg-yellow-300 text-yellow-900" title="{{ $consent_statistics_activity ?? '' }}">{{ trans('consent.enabled') }}</span> @endif</label>
			<span class="description text-gray-700">
				{{trans('consent.statistics.dialog_description')}}
			</span>
			@if( $errors->has('statistics') )
				<span class="field-error">{{ implode(",", $errors->get('statistics'))  }}</span>
			@endif
		</div>
	</div>
	

@stop

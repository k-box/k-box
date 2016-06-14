
@if(!is_null(auth()->user()) && !auth()->user()->optionTermsAccepted())
	
	<div class="alert warning alert__choice alert__hidden js-terms-accept-dialog">
		
		{!! trans('notices.terms_of_use', ['policy_link' => route('terms')]) !!}
		
		<button class="primary" data-action="accept">{{ trans('actions.got_it') }}</button>
		<button data-action="close">{{ trans('panels.close_btn') }}</button>
		
	</div>

@endif

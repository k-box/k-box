
@if(!is_null(auth()->user()) && !auth()->user()->optionTermsAccepted())
	
	<div class="c-message c-message--warning c-message--topmost alert__hidden js-terms-accept-dialog">
		
		{!! trans('notices.terms_of_use', ['policy_link' => route('terms')]) !!}
		
		<button class="button" data-action="accept">{{ trans('actions.got_it') }}</button>
		<button class="button" data-action="close">{{ trans('panels.close_btn') }}</button>
		
	</div>

@endif

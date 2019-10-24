@php
	$config = \KBox\Support\SupportService::service();
	$contact_mail = $config['address'] ?? null;
	$support_subject = __('Write here your request');
	$support_body = "%0A%0A%0Aproduct: $product, version: $version, route: $route, user: $feedback_user";
@endphp
@if($contact_mail)
<a class="{{ $class ?? '' }}" target="_blank" rel="nopener noreferrer" href="mailto:{{$contact_mail}}?subject={{ $support_subject }}&body={{ $support_body }}">{{trans('actions.contact_support')}}</a>
@endif
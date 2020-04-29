



@includeWhen(support_active('uservoice'), 'support.uservoice', [
	'feedback_loggedin' => $feedback_loggedin ?? false,
	'feedback_user_mail' => $feedback_user_mail ?? null,
	'feedback_user_name' => $feedback_user_name ?? null,
	'product' => config('app.name'),
	'version' => config("dms.version"),
	'route' => ! is_null(\Route::getCurrentRoute()->getName()) ? \Route::getCurrentRoute()->getName() : \Route::getCurrentRoute()->getPath(),
	'context' => isset($context) ? e($context) : null,
	'group' => isset($context_group) ? e($context_group) : null,
	'visibility' => isset($current_visibility) ? e($current_visibility) : null,
	'search_terms' => isset($search_terms) ? e($search_terms) : null,
])

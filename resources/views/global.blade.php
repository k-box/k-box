@extends('layout.shell')

@section('application')
			<div id="app" class="flex flex-col h-screen max-h-screen">

			<div class="long-running-message" id="long-running-message">
				{!! trans('notices.long_running_msg') !!}
			</div>


			@section('header')
				@include('headers.header')
			@endsection
	
			@yield('header')

			@if(is_readonly())
				<div class="bg-yellow-400 p-2">
					{!!trans('errors.503-readonly_text_styled')!!}
				</div>
			@endif
	
			<!--[if lte IE 10]>
				<div class="" id="js-outdated">
					<div class="bg-yellow-200 p-2 ">
						<span class="">
							{{ trans('errors.oldbrowser.generic') }}
							<a href="{{route('browserupdate')}}" class="text-black underline">{{ trans('errors.oldbrowser.more_info') }}</a>.
						</span>
					</div>
				</div>
			<![endif]-->

			<div class="min-h-0 flex-shrink-0 flex-grow " id="page" role="content">
	
				{{-- px-2 lg:px-4 --}}

				@yield('content')
	
			</div>
	
			@yield('footer')
		</div>

		
	


	
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
@endsection

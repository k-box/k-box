

<div class="navigation navigation--secondary">
		
	<a href="{{ route('administration.users.index') }}" class="navigation__item navigation__item--link @if(\Request::is('*users')) navigation__item--current @endif">
		
		@materialicon('social', 'people_outline', 'navigation__item__icon')
		
		{{trans('administration.menu.accounts')}}
	</a>


	<a href="{{ route('administration.institutions.index') }}" class="navigation__item navigation__item--link @if(\Request::is('*institutions')) navigation__item--current @endif">
		
		@materialicon('action', 'account_balance', 'navigation__item__icon')
		
		{{trans('administration.menu.institutions')}}
	</a>

	<a href="{{ route('administration.storage.index') }}" class="navigation__item navigation__item--link @if(\Request::is('*storage*')) navigation__item--current @endif">
			
		@materialicon('action', 'dns', 'navigation__item__icon')
		
		{{trans('administration.menu.storage')}}
	</a>
	
	<a href="{{ route('administration.licenses.index') }}" class="navigation__item navigation__item--link @if(\Request::is('*licenses')) navigation__item--current @endif">
			
		@materialicon('action', 'copyright', 'navigation__item__icon')
		
		{{trans('administration.menu.licenses')}}
	</a>

	<a href="{{ route('administration.network.index') }}" class="navigation__item navigation__item--link @if(\Request::is('*network')) navigation__item--current @endif">
			
		@materialicon('action', 'settings_ethernet', 'navigation__item__icon')
		
		{{trans('administration.menu.network')}}
	</a>
	
	<a href="{{ route('administration.mail.index') }}" class="navigation__item navigation__item--link @if(\Request::is('*mail')) navigation__item--current @endif">
			
		@materialicon('content', 'mail', 'navigation__item__icon')
		
		{{trans('administration.menu.mail')}}
	</a>

	<a href="{{ route('administration.settings.index') }}" class="navigation__item navigation__item--link @if(\Request::is('*settings')) navigation__item--current @endif">
			
		@materialicon('action', 'settings', 'navigation__item__icon')
		
		{{trans('administration.menu.settings')}}
	</a>

	<a href="{{ route('administration.analytics.index') }}" class="navigation__item navigation__item--link @if(\Request::is('*analytics')) navigation__item--current @endif">
			
		@materialicon('action', 'timeline', 'navigation__item__icon')
		
		{{trans('administration.menu.analytics')}}
	</a>

	<a href="{{ route('administration.support.index') }}" class="navigation__item navigation__item--link @if(\Request::is('*support')) navigation__item--current @endif">
			
		@materialicon('communication', 'live_help', 'navigation__item__icon')
		
		{{trans('administration.menu.support')}}
	</a>

	@flag(\KBox\Flags::PLUGINS)
	<a href="{{ route('administration.plugins.index') }}" class="navigation__item navigation__item--link @if(\Request::is('*plugins')) navigation__item--current @endif">
			
		@materialicon('hardware', 'device_hub', 'navigation__item__icon')
		
		{{trans('plugins.page_title')}}
	</a>
	@endflag

	<a href="{{ route('administration.identity.index') }}" class="navigation__item navigation__item--link @if(\Request::is('*identity')) navigation__item--current @endif">
		
		<svg class="icon navigation__item__icon" data-icon="actions:settings" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h4l3 3 3-3h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 3.3c1.49 0 2.7 1.21 2.7 2.7 0 1.49-1.21 2.7-2.7 2.7-1.49 0-2.7-1.21-2.7-2.7 0-1.49 1.21-2.7 2.7-2.7zM18 16H6v-.9c0-2 4-3.1 6-3.1s6 1.1 6 3.1v.9z"/></svg>
		
		{{trans('administration.menu.identity')}}
	</a>

	<a href="{{ route('administration.maintenance.index') }}" class="navigation__item navigation__item--link @if(\Request::is('*maintenance')) navigation__item--current @endif">
			
		@materialicon('action', 'report_problem', 'navigation__item__icon')
		
		{{trans('administration.menu.maintenance')}}
	</a>

</div>


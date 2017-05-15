

<ul class="navigation-admin @if(isset($block) && $block) navigation-admin--block @endif">
	
	<li class="navigation-admin__item">
		<a href="{{ route('administration.users.index') }}" class="navigation-admin__link @if(\Request::is('*users')) navigation--current @endif">
			
			<svg class="navigation-admin__item__icon" data-icon="actions:perm_identity" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 5.9c1.16 0 2.1.94 2.1 2.1s-.94 2.1-2.1 2.1S9.9 9.16 9.9 8s.94-2.1 2.1-2.1m0 9c2.97 0 6.1 1.46 6.1 2.1v1.1H5.9V17c0-.64 3.13-2.1 6.1-2.1M12 4C9.79 4 8 5.79 8 8s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 9c-2.67 0-8 1.34-8 4v3h16v-3c0-2.66-5.33-4-8-4z"/></svg>
			
			{{trans('administration.menu.accounts')}}
		</a>
	</li>
	
	<li class="navigation-admin__item">
		<a href="{{ route('administration.institutions.index') }}" class="navigation-admin__link @if(\Request::is('*institutions')) navigation--current @endif">
			
			<svg class="navigation-admin__item__icon" data-icon="actions:account_balance" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M4 10v7h3v-7H4zm6 0v7h3v-7h-3zM2 22h19v-3H2v3zm14-12v7h3v-7h-3zm-4.5-9L2 6v2h19V6l-9.5-5z"/></svg>
			
			{{trans('administration.menu.institutions')}}
		</a>
	</li>

	<li class="navigation-admin__item"><a href="{{ route('administration.storage.index') }}" class="navigation-admin__link @if(\Request::is('*storage')) navigation--current @endif">
			
			<svg class="navigation-admin__item__icon" data-icon="actions:dns" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M20 13H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1zM7 19c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zM20 3H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1zM7 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg>
			
			{{trans('administration.menu.storage')}}
		</a>
	</li>

	<li class="navigation-admin__item"><a href="{{ route('administration.network.index') }}" class="navigation-admin__link @if(\Request::is('*network')) navigation--current @endif">
			
			<svg class="navigation-admin__item__icon" data-icon="actions:settings_ethernet" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M7.77 6.76L6.23 5.48.82 12l5.41 6.52 1.54-1.28L3.42 12l4.35-5.24zM7 13h2v-2H7v2zm10-2h-2v2h2v-2zm-6 2h2v-2h-2v2zm6.77-7.52l-1.54 1.28L20.58 12l-4.35 5.24 1.54 1.28L23.18 12l-5.41-6.52z"/></svg>
			
			{{trans('administration.menu.network')}}
		</a>
	</li>
	
	<li class="navigation-admin__item"><a href="{{ route('administration.mail.index') }}" class="navigation-admin__link @if(\Request::is('*mail')) navigation--current @endif">
			
			<svg class="navigation-admin__item__icon" data-icon="content:mail" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
			
			{{trans('administration.menu.mail')}}
		</a>
	</li>

	<li class="navigation-admin__item"><a href="{{ route('administration.settings.index') }}" class="navigation-admin__link @if(\Request::is('*settings')) navigation--current @endif">
			
			<svg class="navigation-admin__item__icon" data-icon="actions:settings" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19.43 12.98c.04-.32.07-.64.07-.98s-.03-.66-.07-.98l2.11-1.65c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.3-.61-.22l-2.49 1c-.52-.4-1.08-.73-1.69-.98l-.38-2.65C14.46 2.18 14.25 2 14 2h-4c-.25 0-.46.18-.49.42l-.38 2.65c-.61.25-1.17.59-1.69.98l-2.49-1c-.23-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64l2.11 1.65c-.04.32-.07.65-.07.98s.03.66.07.98l-2.11 1.65c-.19.15-.24.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1c.52.4 1.08.73 1.69.98l.38 2.65c.03.24.24.42.49.42h4c.25 0 .46-.18.49-.42l.38-2.65c.61-.25 1.17-.59 1.69-.98l2.49 1c.23.09.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.65zM12 15.5c-1.93 0-3.5-1.57-3.5-3.5s1.57-3.5 3.5-3.5 3.5 1.57 3.5 3.5-1.57 3.5-3.5 3.5z"/></svg>
			
			{{trans('administration.menu.settings')}}
		</a>
	</li>
	
	<li class="navigation-admin__item"><a href="{{ route('administration.identity.index') }}" class="navigation-admin__link @if(\Request::is('*identity')) navigation--current @endif">
			
			<svg class="navigation-admin__item__icon" data-icon="actions:settings" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h4l3 3 3-3h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 3.3c1.49 0 2.7 1.21 2.7 2.7 0 1.49-1.21 2.7-2.7 2.7-1.49 0-2.7-1.21-2.7-2.7 0-1.49 1.21-2.7 2.7-2.7zM18 16H6v-.9c0-2 4-3.1 6-3.1s6 1.1 6 3.1v.9z"/></svg>
			
			{{trans('administration.menu.identity')}}
		</a>
	</li>

	<li class="navigation-admin__item"><a href="{{ route('administration.maintenance.index') }}" class="navigation-admin__link @if(\Request::is('*maintenance')) navigation--current @endif">
			
			<svg class="navigation-admin__item__icon" data-icon="actions:report_problem" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
			
			{{trans('administration.menu.maintenance')}}
		</a>
	</li>

</ul>


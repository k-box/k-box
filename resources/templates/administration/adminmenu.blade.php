

<ul class="blocks @if(isset($small) && $small) small @endif @if(isset($compact) && $compact) compact @endif">
	
	<li>
		<a href="{{ route('administration.users.index') }}" class="block @if(\Request::is('*users')) current @endif">
			<span class="icon">
				<span class="icon-action-black icon-action-black-ic_perm_identity_black_24dp"></span>
			</span>
			<span class="title">{{trans('administration.menu.accounts')}}</span>
		</a>
	</li>
	
	<li>
		<a href="{{ route('administration.institutions.index') }}" class="block @if(\Request::is('*institutions')) current @endif">
			<span class="icon">
				<span class="icon-action-black icon-action-black-ic_account_balance_black_24dp"></span>
			</span>
			<span class="title">{{trans('administration.menu.institutions')}}</span>
		</a>
	</li>

	<li><a href="{{ route('administration.languages.index') }}" class="block @if(\Request::is('*languages')) current @endif">
			<span class="icon">
				<span class="icon-action-black icon-action-black-ic_language_black_24dp"></span>
			</span>
			<span class="title">{{trans('administration.menu.language')}}</span>
		</a>
	</li>

	<li><a href="{{ route('administration.storage.index') }}" class="block @if(\Request::is('*storage')) current @endif">
			<span class="icon">
				<span class="icon-action-black icon-action-black-ic_dns_black_24dp"></span>
			</span>
			<span class="title">{{trans('administration.menu.storage')}}</span>
		</a>
	</li>

	<li><a href="{{ route('administration.network.index') }}" class="block @if(\Request::is('*network')) current @endif">
			<span class="icon">
				<span class="icon-action-black icon-action-black-ic_settings_ethernet_black_24dp"></span>
			</span>
			<span class="title">{{trans('administration.menu.network')}}</span>
		</a>
	</li>
	
	<li><a href="{{ route('administration.mail.index') }}" class="block @if(\Request::is('*mail')) current @endif">
			<span class="icon">
				<span class="icon-content-black icon-content-black-ic_mail_black_24dp"></span>
			</span>
			<span class="title">{{trans('administration.menu.mail')}}</span>
		</a>
	</li>

	<li><a href="{{ route('administration.settings.index') }}" class="block @if(\Request::is('*settings')) current @endif">
			<span class="icon">
				<span class="icon-action-black icon-action-black-ic_settings_black_24dp"></span>
			</span>
			<span class="title">{{trans('administration.menu.settings')}}</span>
		</a>
	</li>

	<li><a href="{{ route('administration.maintenance.index') }}" class="block @if(\Request::is('*maintenance')) current @endif">
			<span class="icon">
				<span class="icon-action-black icon-action-black-ic_report_problem_black_24dp"></span>
			</span>
			<span class="title">{{trans('administration.menu.maintenance')}}</span>
		</a>
	</li>

</ul>


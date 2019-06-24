@extends('global')

@section('breadcrumbs')

	<span class="breadcrumb__item--current">{{trans('administration.page_title')}}</span>

@stop


@section('action-menu')



@stop

@section('content')		

	<div class="c-column c-column--medium">


		<ul class="navigation-admin navigation-admin--block">
			
			<li class="navigation-admin__item">
				<a href="{{ route('administration.users.index') }}" class="navigation-admin__link @if(\Request::is('*users')) navigation--current @endif">
					
					@materialicon('social', 'people_outline', 'navigation-admin__item__icon')
					
					{{trans('administration.menu.accounts')}}
				</a>
			</li>
			
			<li class="navigation-admin__item">
				<a href="{{ route('administration.institutions.index') }}" class="navigation-admin__link @if(\Request::is('*institutions')) navigation--current @endif">
					
					@materialicon('action', 'account_balance', 'navigation-admin__item__icon')
					
					{{trans('administration.menu.institutions')}}
				</a>
			</li>

			<li class="navigation-admin__item"><a href="{{ route('administration.storage.index') }}" class="navigation-admin__link @if(\Request::is('*storage')) navigation--current @endif">
					
					@materialicon('action', 'dns', 'navigation-admin__item__icon')
					
					{{trans('administration.menu.storage')}}
				</a>
			</li>

			<li class="navigation-admin__item">
				<a href="{{ route('administration.licenses.index') }}" class="navigation-admin__link @if(\Request::is('*licenses')) navigation__item--current @endif">
				
					@materialicon('action', 'copyright', 'navigation-admin__item__icon')
					
					{{trans('administration.menu.licenses')}}
				</a>
			</li>

			<li class="navigation-admin__item"><a href="{{ route('administration.network.index') }}" class="navigation-admin__link @if(\Request::is('*network')) navigation--current @endif">
					
					@materialicon('action', 'settings_ethernet', 'navigation-admin__item__icon')
					
					{{trans('administration.menu.network')}}
				</a>
			</li>
			
			<li class="navigation-admin__item"><a href="{{ route('administration.mail.index') }}" class="navigation-admin__link @if(\Request::is('*mail')) navigation--current @endif">
					
					@materialicon('content', 'mail', 'navigation-admin__item__icon')
					
					{{trans('administration.menu.mail')}}
				</a>
			</li>

			<li class="navigation-admin__item"><a href="{{ route('administration.settings.index') }}" class="navigation-admin__link @if(\Request::is('*settings')) navigation--current @endif">
					
					@materialicon('action', 'settings', 'navigation-admin__item__icon')
					
					{{trans('administration.menu.settings')}}
				</a>
			</li>
			
			<li class="navigation-admin__item"><a href="{{ route('administration.analytics.index') }}" class="navigation-admin__link @if(\Request::is('*analytics')) navigation--current @endif">
					
					@materialicon('action', 'timeline', 'navigation-admin__item__icon')
					
					{{trans('administration.menu.analytics')}}
				</a>
			</li>

			@flag(\KBox\Flags::PLUGINS)
				<li class="navigation-admin__item"><a href="{{ route('administration.plugins.index') }}" class="navigation-admin__link @if(\Request::is('*plugins')) navigation--current @endif">	
						@materialicon('hardware', 'device_hub', 'navigation-admin__item__icon')
						
						{{trans('plugins.page_title')}}
					</a>
				</li>
			@endflag
			
			<li class="navigation-admin__item"><a href="{{ route('administration.identity.index') }}" class="navigation-admin__link @if(\Request::is('*identity')) navigation--current @endif">
					
					<svg class="navigation-admin__item__icon" data-icon="actions:settings" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h4l3 3 3-3h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 3.3c1.49 0 2.7 1.21 2.7 2.7 0 1.49-1.21 2.7-2.7 2.7-1.49 0-2.7-1.21-2.7-2.7 0-1.49 1.21-2.7 2.7-2.7zM18 16H6v-.9c0-2 4-3.1 6-3.1s6 1.1 6 3.1v.9z"/></svg>
					
					{{trans('administration.menu.identity')}}
				</a>
			</li>

			<li class="navigation-admin__item"><a href="{{ route('administration.maintenance.index') }}" class="navigation-admin__link @if(\Request::is('*maintenance')) navigation--current @endif">
					
					@materialicon('action', 'report_problem', 'navigation-admin__item__icon')
					
					{{trans('administration.menu.maintenance')}}
				</a>
			</li>

		</ul>

	</div>

	<div class="c-column c-column--short widgets">

		@include('dashboard.notices')

		@include('widgets.storage')

		@include('widgets.users-sessions')

	</div>

@stop
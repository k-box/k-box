
@extends('layout.sidebar')

@section('breadcrumbs')
        
    {{trans('profile.page_title', ['name' => $current_user_name])}}

@stop

@section('sidebar')

<div class="navigation navigation--secondary">
		
	<a href="{{ route('profile.index') }}" class="navigation__item navigation__item--link">
		@component('avatar.full', ['image' => auth()->user()->avatar, 'name' => $current_user_name])
			{{$current_user_name}}
		@endcomponent
	</a>
	<a href="{{ route('profile.index') }}" class="navigation__item navigation__item--link @if(request()->is('*profile')) navigation__item--current @endif">
		
		@materialicon('action', 'account_circle', 'inline-block navigation__item__icon')
		
		{{trans('profile.profile')}}
	</a>
	<a href="{{ route('profile.privacy.index') }}" class="navigation__item navigation__item--link @if(request()->is('*profile/privacy')) navigation__item--current @endif">
		
		@materialicon('hardware', 'security', 'inline-block navigation__item__icon')
		
		{{trans('profile.privacy.privacy')}}
	</a>
	<a href="{{ route('profile.email.index') }}" class="navigation__item navigation__item--link @if(request()->is('*profile/email')) navigation__item--current @endif">
		
		@materialicon('communication', 'mail_outline', 'inline-block navigation__item__icon')
		
		{{trans('profile.email_section')}}
	</a>
	<a href="{{ route('profile.password.index') }}" class="navigation__item navigation__item--link @if(request()->is('*profile/password')) navigation__item--current @endif">
		
		@materialicon('action', 'lock', 'inline-block navigation__item__icon')
		
		{{trans('profile.password_section')}}
	</a>
	<a href="{{ route('profile.data-export.index') }}" class="navigation__item navigation__item--link @if(request()->is('*profile/data-export')) navigation__item--current @endif">
		
		@materialicon('action', 'get_app', 'inline-block navigation__item__icon')
		
		{{trans('profile.export_section')}}
	</a>

</div>
	
@endsection


@section('page')

	@include('errors.list')

	@yield('profile_page')

@stop

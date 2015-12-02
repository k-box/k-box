
@if($is_user_logged)

	<div class="avatar">

	<div class="profile">
		<span class="user-name">{{$user_name}}</span>
		<span class="user-type"><a href="{{ url('auth/logout') }}">
				{{trans('login.logout')}}
			</a></span>
	</div>

	@include('avatar.picture')

	</div>

@endif
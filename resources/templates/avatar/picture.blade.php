<div class="profile-pic @if(isset($inline) && $inline) inline @endif" style="background-color:{{$avatar_color}}">

	@if(!isset($no_link))
	<a href="{{route('profile.index')}}" title="{{trans('profile.go_to_profile')}}">
	@endif

	@if(isset($image))

		<img src="{{$image}}">

	@else

		<span class="initials">{{$user_initial}}</span>

	@endif

	@if(!isset($no_link))
	</a>
	@endif
</div>
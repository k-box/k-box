@if(isset($image) && is_string($image))

	<img class="avatar__picture" src="{{$image}}">

@elseif(isset($name) && is_string($name))

	<span class="avatar__initials">{{ mb_substr(studly_case($name), 0, 1) }}</span>

@elseif(isset($icon))

	{{ $icon }}

@else

	@materialicon('social', 'person', 'avatar__icon')

@endif

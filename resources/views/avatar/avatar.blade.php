<div class="avatar @if(isset($inline) && $inline) avatar--inline @endif">
	@if(isset($url))
	<a href="{{ $url }}" title="{{ $alt or '' }}">
	@endif

	@component('avatar.picture', ['image' => isset($image) ? $image : null, 'name' => isset($name) ? $name : null, 'icon' => isset($icon) ? $icon : null])

	@endcomponent

	@if(isset($url))
	</a>
	@endif
</div>

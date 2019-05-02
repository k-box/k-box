<div class="avatar relative {{ isset($inline) && $inline ? 'inline' : 'inline-block' }} rounded-full overflow-hidden flex-shrink-0 h-10 w-10">
	@if(isset($url) && $url)
	<a href="{{ $url }}" title="{{ $alt ?? '' }}">
	@endif

	@component('avatar.picture', ['image' => isset($image) ? $image : null, 'name' => isset($name) ? $name : null, 'icon' => isset($icon) ? $icon : null])

	@endcomponent

	@if(isset($url) && $url)
	</a>
	@endif
</div>

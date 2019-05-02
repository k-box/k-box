<div class="flex items-center">

	@component('avatar.avatar', ['image' => isset($image) ? $image : null, 'name' => isset($name) ? $name : null, 'icon' => isset($icon) ? $icon : null])

	@endcomponent
	
	<div class="ml-4">{{$slot}}</div>

</div>

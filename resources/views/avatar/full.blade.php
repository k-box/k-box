<div class="avatar-container">

	@component('avatar.avatar', ['image' => isset($image) ? $image : null, 'name' => isset($name) ? $name : null, 'icon' => isset($icon) ? $icon : null])

	@endcomponent
	
	<div class="avatar__label">{{$slot}}</div>

</div>

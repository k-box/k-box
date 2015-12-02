
<div class="colorpicker">
	
	<ul class="colors">

		@foreach($colors as $color_name => $color_value)

			<li>
				<input type="radio" id="{{$color_name}}" name="color" title="{{$color_name}}"  value="{{$color_value}}" @if($selected == $color_value) checked="checked" @endif />
				<label for="{{$color_name}}" class="pick {{strtolower($color_name)}}"></label>
			</li>

		@endforeach

	</ul>

</div>
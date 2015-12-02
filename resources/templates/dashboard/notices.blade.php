

@if(isset($notices))

<div class="row">
	
	@foreach($notices as $notice)

		<div class="alert info">
			{!!$notice!!}
		</div>

	@endforeach

</div>

@endif
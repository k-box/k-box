

@if(isset($notices))

<div class="row">
	
	@foreach($notices as $notice)

		<div class="c-message">
			{!!$notice!!}
		</div>

	@endforeach

</div>

@endif
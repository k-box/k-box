

@if(isset($notices))

<div class="row">

	@foreach($error_notices as $notice)

		<div class="c-message c-message--error">
			{!!$notice!!}
		</div>

	@endforeach
	
	@foreach($notices as $notice)

		<div class="c-message">
			{!!$notice!!}
		</div>

	@endforeach

</div>

@endif
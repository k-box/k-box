

@if(isset($notices))

<div class="row">

	@if(isset($error_notices))
		@foreach($error_notices as $notice)
		
		<div class="c-message c-message--error">
			{!!$notice!!}
		</div>
		
		@endforeach
	@endif
	
	@if(isset($notices))
		@foreach($notices as $notice)
		
			<div class="c-message">
				{!!$notice!!}
			</div>
		
		@endforeach
	@endif

</div>

@endif
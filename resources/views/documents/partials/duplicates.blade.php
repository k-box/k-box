

<div class="duplicates-container">

	<h4 class="c-section__title" id="versions">@materialicon('content', 'content_copy', 'button__icon c-section__icon')
		{{trans('documents.duplicates.duplicates_btn')}}
    </h4>
    
    <span class="description">
        {{trans('documents.duplicates.duplicates_description')}}
    </span>
	
	<div class="c-form__field version-list">
		
		@foreach($duplicates as $duplicate)
			
			@include('documents.partials.duplicate', ['duplicate' => $duplicate])

		@endforeach
	</div>

</div>
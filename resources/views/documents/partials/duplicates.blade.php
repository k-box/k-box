

<div class="duplicates-container">

	<h4 class="my-4" id="versions">@materialicon('content', 'content_copy', 'button__icon  __icon')
		{{trans('documents.duplicates.duplicates_btn')}}
    </h4>
    
    <span class="description">
        {{trans('documents.duplicates.duplicates_description')}}
    </span>
	
	<div class=" mb-4 version-list">
		
		@foreach($duplicates as $duplicate)
			
			@include('documents.partials.duplicate', ['duplicate' => $duplicate])

		@endforeach
	</div>

</div>
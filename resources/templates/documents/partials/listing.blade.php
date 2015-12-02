
@if((isset($documents_count) && $documents_count > 0) || $documents->count() > 0)

		@if(isset($collections))
		@foreach ($collections as $result)

			@include('documents.descriptor', ['item' => $result])

		@endforeach
		@endif

		@foreach ($documents as $result)

			@include('documents.descriptor', ['item' => $result])

		@endforeach

	@else

		@if(isset($empty_message))

			<p>{!!$empty_message!!}</p>

		@else

			<p>No documents, start upload something (you can use the drag and drop)</p>

		@endif
		

	@endif
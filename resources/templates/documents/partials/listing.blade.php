
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

			<p>{{ trans('document.messages.no_documents') }}</p>

		@endif
		

	@endif
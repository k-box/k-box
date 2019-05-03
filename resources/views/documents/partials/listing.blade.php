
@if((isset($documents_count) && $documents_count > 0) || $documents && $documents->count() > 0)

		@if(isset($collections))
		@foreach ($collections as $result)

			@include('documents.descriptor', ['item' => $result])

		@endforeach
		@endif

		@foreach ($documents as $result)

			@include('documents.descriptor', ['item' => $result])

		@endforeach

	@elseif(isset($is_search_failed) && $is_search_failed)

			<div class="c-message c-message--error">
				<p>{{ trans('search.failure') }}</p>
			</div>
	@else


		<div class="empty">

			@materialicon('action', 'description', 'empty__icon')

			@if(isset($empty_message))

				<p class="empty__message">{!!$empty_message!!}</p>

			@else

				<p class="empty__message">{{ trans('documents.messages.no_documents') }}</p>

			@endif

		</div>
		

	@endif
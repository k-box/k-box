@extends('documents.document-layout')


@section('page-action')


@stop

@section('document_area')
			
		@forelse ($starred as $descriptor)

			@if(class_basename(get_class($descriptor)) === 'Starred')

				@include('documents.descriptor', ['item' => $descriptor->document, 'star_id' => $descriptor->id])

			@else

				@include('documents.descriptor', ['item' => $descriptor, 'star_id' => $descriptor->stars ? $descriptor->stars->first()->id : null])

			@endif

		@empty

			<div class="empty">

				@materialicon('toggle', 'star_border', 'empty__icon')

				@if(isset($empty_message))

					<p class="empty__message">{{ $empty_message }}</p>

				@else

					<p class="empty__message">{{ trans('starred.empty_message') }}</p>

				@endif

			</div>

		@endforelse


@stop


@section('document_script_initialization')

	@if(isset($can_upload) && $can_upload)
		Documents.initUploadService();
	@endif

@stop
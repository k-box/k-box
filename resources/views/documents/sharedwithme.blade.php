@extends('documents.document-layout')


@section('page-action')


@stop


@section('list_header')
	<x-column-header class="list__column list__column--large" key="name" :sort="$sorting ?? null">
		{{trans('documents.descriptor.name')}}
	</x-column-header>

	@unless (request()->hasSearch())
		<x-column-header class="list__column" key="shared_by" :sort="$sorting ?? null">
			{{trans('share.shared_by_label')}}
		</x-column-header>
		
		<x-column-header class="list__column" key="shared_date" :sort="$sorting ?? null">
			{{trans('share.shared_on')}}
		</x-column-header>
	@endunless

	@if (request()->hasSearch())
		<x-column-header class="list__column list__column--hideable">
			{{trans('documents.descriptor.added_by')}}
		</x-column-header>
		
		<x-column-header class="list__column" key="update_date" :sort="$sorting ?? null">
			{{trans('documents.descriptor.last_modified')}}
		</x-column-header>
	@endif
	
	<x-column-header class="list__column list__column--hideable">
		{{trans('documents.descriptor.language')}}
	</x-column-header>
@endsection

@section('document_area')
	
	@if(!is_null($shared_with_me))

		@forelse ($shared_with_me as $result)
	
			@if(is_a($result, 'KBox\Shared'))
			
	
				@include('documents.descriptor', ['item' => $result->shareable, 'share_id' => $result->id, 'shared_by' => $result->user, 'share_created_at' => $result->created_at])
			
			@else
			
				@include('documents.descriptor', ['item' => $result])
			
			@endif
	
		@empty

			<div class="empty">
				@materialicon('social', 'share', 'empty__icon')

				<p>{{ trans('share.empty_with_me_message') }}</p>
			</div>
	
		@endforelse
	@else
		<div class="empty">
			@materialicon('social', 'share', 'empty__icon')

			<p>{{ trans('share.empty_with_me_message') }}</p>
		</div>
	@endif


@stop


@section('document_script_initialization')

	@if(isset($can_upload) && $can_upload)
		Documents.initUploadService();
	@endif

@stop


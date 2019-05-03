@extends('documents.document-layout')


@section('page-action')


@stop

@section('additional_filter_buttons')
	

	@unless(isset($is_search_requested) && $is_search_requested)
		<div class="page-actions__label hint--bottom" data-hint="{{ trans('actions.sort_by.label') }}">
			<a href="?o=a" class="button @if(isset($order) && $order==='ASC') button--selected @endif">{{ trans('actions.sort_by.oldest_first') }}</a>
			<a href="?o=d" class="button @if(isset($order) && $order==='DESC') button--selected @endif">{{ trans('actions.sort_by.newest_first') }}</a>
		</div>
	@endif
		
	
@stop


@section('document_list_area')
	
	@if(!is_null($shared_with_me))

		<div class="list {{$list_style_current}}" >

		<div class="list__header">
			<div class="list__column list__column--large">{{trans('documents.descriptor.name')}}</div>
			<div class="list__column">{{trans('share.shared_by_label')}}</div>
			<div class="list__column">{{trans('share.shared_on')}}</div>
			<div class="list__column">{{trans('documents.descriptor.language')}}</div>
		</div>

			@forelse ($shared_with_me as $result)
		
				@if(is_a($result, 'KBox\Shared'))
		
					@include('documents.descriptor', ['item' => $result->shareable, 'share_id' => $result->id, 'shared_by' => $result->user, 'share_created_at' => $result->getCreatedAt(), 'share_created_at_timestamp' => $result->getCreatedAt(true)])
				
				@else
				
					@include('documents.descriptor', ['item' => $result])
				
				@endif
		
			@empty

				<div class="empty">
					@materialicon('social', 'share', 'empty__icon')

					<p>{{ trans('share.empty_with_me_message') }}</p>
				</div>
		
			@endforelse

		</div>
        
        @if( isset($pagination) && !is_null($pagination) )
            <div class="pagination-container">

                {!! $pagination->render() !!}

            </div>
        @endif
	
	@endif


@stop


@section('document_script_initialization')

	@if(isset($can_upload) && $can_upload)
		Documents.initUploadService();
	@endif

@stop


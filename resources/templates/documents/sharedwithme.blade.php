@extends('documents.document-layout')


@section('page-action')

@if($can_share)
<!--<a href="#unshare" class="button disabled" rv-on-click="unshare">
		Edit share
	</a>-->
	<!--<a href="#unshare" class="button" rv-on-click="unshare">
		Unshare
	</a>-->
@endif

@stop

@section('additional_filter_buttons')
	<div class="page-actions__container page-actions--shared-page">

		@unless(isset($is_search_requested) && $is_search_requested)
			<span class="page-actions__label hint--bottom" data-hint="{{ trans('actions.sort_by.label') }}">
				<a href="?o=a" class="button page-actions__action page-actions__action--grouped @if(isset($order) && $order==='ASC') page-actions__action--selected @endif">{{ trans('actions.sort_by.oldest_first') }}</a>
				<a href="?o=d" class="button page-actions__action page-actions__action--grouped @if(isset($order) && $order==='DESC') page-actions__action--selected @endif">{{ trans('actions.sort_by.newest_first') }}</a>
			</span>
		@endif
		
	</div>
@stop

@section('document_list_area')



	@if(isset($shared_by_me) && !is_null($shared_by_me))

	<div class="share-section shared-by-me clearfix">

		<div>

			@include('avatar.picture')

			<h5 class="title">{{trans('share.shared_by_me_title')}}</h5>
			<span class="description">{{trans_choice('share.shared_by_me_count', $shared_by_me->count(), ['num' => $shared_by_me->count()])}}</span>
		</div>

		<div class="list {{$list_style_current}}" >

			@forelse ($shared_by_me as $result)
		
				@include('documents.descriptor', ['item' => $result->shareable, 'share_id' => $result->id, 'shared_with' => $result->sharedwith])
		
			@empty
		
				<p>{{ trans('share.empty_by_me_message') }}</p>
		
			@endforelse

		</div>

	</div>
	
	@endif
	
	@if(!is_null($shared_with_me))

	<div class="share-section shared-with-me clearfix">

		<div class="list {{$list_style_current}}" >

			@forelse ($shared_with_me as $result)
		
				@if(is_a($result, 'KlinkDMS\Shared'))
		
					@include('documents.descriptor', ['item' => $result->shareable, 'share_id' => $result->id, 'shared_by' => $result->user, 'share_created_at' => $result->getCreatedAt(), 'share_created_at_timestamp' => $result->getCreatedAt(true)])
				
				@else
				
					@include('documents.descriptor', ['item' => $result])
				
				@endif
		
			@empty

				<div class="empty">
					<svg class="empty__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 3c-2.33 0-4.31 1.46-5.11 3.5h10.22c-.8-2.04-2.78-3.5-5.11-3.5z"/></svg>

					<p>{{ trans('share.empty_with_me_message') }}</p>
				</div>
		
			@endforelse

		</div>
        
        @if( isset($pagination) && !is_null($pagination) )
            <div class="pagination-container">

                {!! $pagination->render() !!}

            </div>
        @endif

	</div>
	
	@endif


@stop


@section('document_script_initialization')

	@if(isset($can_upload) && $can_upload)
		Documents.initUploadService();
	@endif

@stop


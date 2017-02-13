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
		
					@include('documents.descriptor', ['item' => $result->shareable, 'share_id' => $result->id, 'shared_by' => $result->user])
				
				@else
				
					@include('documents.descriptor', ['item' => $result])
				
				@endif
		
			@empty
		
				<p>{{ trans('share.empty_with_me_message') }}</p>
		
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


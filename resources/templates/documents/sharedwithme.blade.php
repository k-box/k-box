@extends('documents.document-layout')


@section('page-action')

@if($can_share)
<!--<a href="#unshare" class="button disabled" rv-on-click="unshare">
		Edit share
	</a>-->
	<a href="#unshare" class="button" rv-on-click="unshare">
		Unshare
	</a>
@endif

@stop

@section('document_list_area')


	@if(!is_null($shared_by_me))

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

		<div>
			<h5 class="title">{{trans('share.shared_with_me_title')}}</h5>
		</div>


		<div class="list {{$list_style_current}}" >

			@forelse ($shared_with_me as $result)
		
				@include('documents.descriptor', ['item' => $result->shareable, 'share_id' => $result->id, 'shared_by' => $result->user])
		
			@empty
		
				<p>{{ trans('share.empty_with_me_message') }}</p>
		
			@endforelse

		</div>

	</div>
	
	@endif


@stop


@section('document_script_initialization')

@stop


@extends('documents.document-layout')

@section('page-action')

<div class="action-group">



		</div>

@stop


@section('document_area')

	<div class="page-actions">

		@if(isset($info_message) && !is_null($info_message))
		<div class="page-actions__message">
			<span class="btn-icon icon-action-black icon-action-black-ic_info_outline_black_24dp"></span> {{$info_message}}
		</div>
		@endif

        <div class="page-actions__container">

			@if(flags()->isUnifiedSearchEnabled())

			@unless(isset($is_search_requested) && $is_search_requested)
				<span class="page-actions__label hint--bottom" data-hint="{{ trans('actions.sort_by.label') }}">
					<a href="?o=a" class="button page-actions__action page-actions__action--grouped @if($order==='ASC') page-actions__action--selected @endif">{{ trans('actions.sort_by.oldest_first') }}</a>
					<a href="?o=d" class="button page-actions__action page-actions__action--grouped @if($order==='DESC') page-actions__action--selected @endif">{{ trans('actions.sort_by.newest_first') }}</a>
				</span>
			@endif

			<span class="page-actions__label hint--bottom" data-hint="{{ trans('documents.filtering.date_range_hint') }}">
					<a href="{{ route('documents.recent', array_merge(['range' => 'today'], $search_replica_parameters)) }}" class="button page-actions__action page-actions__action--grouped @if($range==='today') page-actions__action--selected @endif">{{ trans('documents.filtering.today') }}</a>
					<a href="{{ route('documents.recent', array_merge(['range' => 'yesterday'], $search_replica_parameters)) }}" class="button page-actions__action page-actions__action--grouped @if($range==='yesterday') page-actions__action--selected @endif">{{ trans('documents.filtering.yesterday') }}</a>
					<a href="{{ route('documents.recent', array_merge(['range' => 'currentweek'], $search_replica_parameters)) }}" class="button page-actions__action page-actions__action--grouped @if($range==='currentweek') page-actions__action--selected @endif">{{ trans('documents.filtering.currentweek') }}</a>
					<a href="{{ route('documents.recent', array_merge(['range' => 'currentmonth'], $search_replica_parameters)) }}" class="button page-actions__action page-actions__action--grouped @if($range==='currentmonth') page-actions__action--selected @endif">{{ trans('documents.filtering.currentmonth') }}</a> 
			</span>

			<span class="page-actions__label hint--bottom" data-hint="{{ trans('documents.filtering.items_per_page_hint') }}">
					<a href="{{ route('documents.recent', array_merge(['range' => $range, 'n' => 12], $search_replica_parameters)) }}" class="button page-actions__action page-actions__action--grouped @if(auth()->user()->optionItemsPerPage() == 12) page-actions__action--selected @endif">12</a>
					<a href="{{ route('documents.recent', array_merge(['range' => $range, 'n' => 24], $search_replica_parameters)) }}" class="button page-actions__action page-actions__action--grouped @if(auth()->user()->optionItemsPerPage() == 24) page-actions__action--selected @endif">24</a>
					<a href="{{ route('documents.recent', array_merge(['range' => $range, 'n' => 50], $search_replica_parameters)) }}" class="button page-actions__action page-actions__action--grouped @if(auth()->user()->optionItemsPerPage() == 50) page-actions__action--selected @endif">50</a>
			</span>

			@endif
            
        </div>
	</div>

@if(empty($groupings))

	<p>{!! trans('documents.empty_msg_recent', ['range' => trans('documents.filtering.' . $range)]) !!}</p>

@else 

	@foreach($groupings as $group)

		<div class="share-section shared-by-me clearfix">

			<div>

				<h5 class="title">{{$group}}</h5>
				
			</div>

			<div class="list {{$list_style_current}}" >

		@include('documents.partials.listing', ['documents' => $documents[$group], 'documents_count' => count($documents[$group])])

			</div>

		</div>

	@endforeach

@endif
	

@stop

@section('document_script_initialization')

	@if(isset($can_upload) && $can_upload)
		Documents.initUploadService();
	@endif

@stop

@extends('documents.document-layout')

@section('page-action')


@stop


@section('document_list_area')

	<div class="page-actions">

			@unless(isset($is_search_requested) && $is_search_requested)
				<div class="page-actions__label hint--bottom" data-hint="{{ trans('actions.sort_by.label') }}">
					<a href="?o=a" class="button @if($order==='ASC') button--selected @endif">{{ trans('actions.sort_by.oldest_first') }}</a>
					<a href="?o=d" class="button @if($order==='DESC') button--selected @endif">{{ trans('actions.sort_by.newest_first') }}</a>
				</div>
			@endif

			<div class="page-actions__label hint--bottom" data-hint="{{ trans('documents.filtering.date_range_hint') }}">
					<a href="{{ route('documents.recent', array_merge(['range' => 'today'], $search_replica_parameters)) }}" class="button @if($range==='today') button--selected @endif">{{ trans('documents.filtering.today') }}</a>
					<a href="{{ route('documents.recent', array_merge(['range' => 'yesterday'], $search_replica_parameters)) }}" class="button @if($range==='yesterday') button--selected @endif">{{ trans('documents.filtering.yesterday') }}</a>
					<a href="{{ route('documents.recent', array_merge(['range' => 'currentweek'], $search_replica_parameters)) }}" class="button @if($range==='currentweek') button--selected @endif">{{ trans('documents.filtering.currentweek') }}</a>
					<a href="{{ route('documents.recent', array_merge(['range' => 'currentmonth'], $search_replica_parameters)) }}" class="button @if($range==='currentmonth') button--selected @endif">{{ trans('documents.filtering.currentmonth') }}</a> 
			</div>

			<div class="page-actions__label hint--bottom" data-hint="{{ trans('documents.filtering.items_per_page_hint') }}">
					<a href="{{ route('documents.recent', array_merge(['range' => $range, 'n' => 12], $search_replica_parameters)) }}" class="button @if(auth()->user()->optionItemsPerPage() == 12) button--selected @endif">12</a>
					<a href="{{ route('documents.recent', array_merge(['range' => $range, 'n' => 24], $search_replica_parameters)) }}" class="button @if(auth()->user()->optionItemsPerPage() == 24) button--selected @endif">24</a>
					<a href="{{ route('documents.recent', array_merge(['range' => $range, 'n' => 50], $search_replica_parameters)) }}" class="button @if(auth()->user()->optionItemsPerPage() == 50) button--selected @endif">50</a>
			</div>
		
	</div>
	
	@if(isset($info_message) && !is_null($info_message))
	<div class="c-message">
		@materialicon('action', 'info_outline', 'button__icon'){{$info_message}}
	</div>
	@endif

	@if(empty($groupings))

		<div class="empty">

			@materialicon('action', 'history', 'empty__icon')

			@if(isset($empty_message))

				<p class="empty__message">{!!$empty_message!!}</p>

			@else

				<p class="empty__message">{!! trans('documents.empty_msg_recent', ['range' => trans('documents.filtering.' . $range)]) !!}</p>

			@endif

		</div>

	@else 

		<div class="list {{$list_style_current}}" >

			<div class="list__header">
					<div class="list__column list__column--large">{{trans('documents.descriptor.name')}}</div>
					<div class="list__column list__column--hideable">{{trans('documents.descriptor.added_by')}}</div>
					<div class="list__column">{{trans('documents.descriptor.last_modified')}}</div>
					<div class="list__column list__column--hideable">{{trans('documents.descriptor.language')}}</div>
			</div>


			@foreach($groupings as $group)

				<div class="list__section">{{$group}}</div>

				@include('documents.partials.listing', ['documents' => $documents[$group], 'documents_count' => count($documents[$group])])

			@endforeach

		</div>
	@endif
	

@stop

@section('document_script_initialization')

	@if(isset($can_upload) && $can_upload)
		Documents.initUploadService();
	@endif

@stop

@extends('documents.document-layout')


@section('additional_actions')

	<div class="page-actions justify-end">

		<div class="page-actions__label" title="{{ trans('documents.filtering.date_range_hint') }}">
				<a href="{{ route('documents.recent', array_merge(['range' => 'today'], $search_replica_parameters)) }}" class="button @if($range==='today') button--selected @endif">{{ trans('documents.filtering.today') }}</a>
				<a href="{{ route('documents.recent', array_merge(['range' => 'yesterday'], $search_replica_parameters)) }}" class="button @if($range==='yesterday') button--selected @endif">{{ trans('documents.filtering.yesterday') }}</a>
				<a href="{{ route('documents.recent', array_merge(['range' => 'currentweek'], $search_replica_parameters)) }}" class="button @if($range==='currentweek') button--selected @endif">{{ trans('documents.filtering.currentweek') }}</a>
				<a href="{{ route('documents.recent', array_merge(['range' => 'currentmonth'], $search_replica_parameters)) }}" class="button @if($range==='currentmonth') button--selected @endif">{{ trans('documents.filtering.currentmonth') }}</a> 
		</div>
	
	</div>

@endsection


@section('document_area')

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

		@foreach($groupings as $group)

			<div class="list__section">{{$group}}</div>

			@include('documents.partials.listing', ['documents' => $documents[$group], 'documents_count' => count($documents[$group])])

		@endforeach

	@endif
	

@stop

@section('document_script_initialization')

	@if(isset($can_upload) && $can_upload)
		Documents.initUploadService();
	@endif

@stop

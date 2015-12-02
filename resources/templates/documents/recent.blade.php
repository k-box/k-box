@extends('documents.document-layout')

@section('page-action')

<div class="action-group">



		</div>

@stop


@section('document_area')



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

	

@stop

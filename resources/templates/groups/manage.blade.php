@extends('documents.document-layout')


@section('page-action')

	

@stop

@section('document_area')

	<div class="tree-view">

	@if($groups_count > 0)

	<ul class="clean-ul">

	@endif

	@forelse($groups as $group)

		@include('groups.tree-item')

	@empty
		
		<p class="description">{{trans('groups.collections.description')}}</p>

		@include('groups.groupform')

	@endforelse


	@if($groups_count > 0)

		<ul>

	@endif

	</div>

@stop


@section('document_script_initialization')

@stop


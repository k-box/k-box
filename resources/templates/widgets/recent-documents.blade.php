@extends('widgets.widget-layout')


@section('widget_title')
{{trans('widgets.recent_docs.title')}}
@overwrite

@section('widget_content')

	<div id="result-list" class="list details">

		@forelse ($recent_documents as $item)

			@include('documents.descriptor', ['item' => $item])

		@empty

			<p>{{trans('widgets.recent_docs.empty')}}</p>

		@endforelse

		<a href="{{route('documents.index')}}">{{trans('widgets.view_all')}}</a>

	</div>

@overwrite

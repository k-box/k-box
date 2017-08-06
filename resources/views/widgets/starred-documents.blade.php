@extends('widgets.widget-layout')


@section('widget_title')

<span class="widget-icon icon-toggle-black icon-toggle-black-ic_star_black_24dp"></span> {{trans('widgets.starred.title')}}

@overwrite

@section('widget_class')
widget-starred
@overwrite

@section('widget_content')


	<div id="result-list" class="list details">

		@forelse ($starred as $item)

			@include('documents.descriptor', ['item' => $item->document, 'hide_checkboxes' => true])

		@empty

			<p>{{trans('widgets.starred.empty')}}</p>

		@endforelse

		<a href="{{route('documents.starred.index')}}">{{trans('widgets.view_all')}}</a>

	</div>


@overwrite
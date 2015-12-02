@extends('widgets.widget-layout')


@section('widget_title')
	{{trans('widgets.recent_searches.title')}}
@overwrite

@section('widget_class')
widget-recent-searches
@overwrite

@section('widget_content')

	<div id="recent-searches-list" class="list">

		@forelse ($recent_searches as $item)

			<div class="item">
				<a href="{{route('search', ['visibility'=> isset($current_visibility) ? $current_visibility : 'public', 's' => $item->terms])}}">{{$item->terms}}</a>
				<span>{{trans('widgets.recent_searches.executed')}} {{$item->updated_at->diffForHumans()}}</span>
			</div>

			

		@empty

			<p>{{trans('widgets.recent_searches.empty')}}</p>

		@endforelse

	</div>

@overwrite

@extends('global')

@section('content')

    <div class="h-5"></div>

	<div class="max-w-4xl">

		<span class="description">{{ trans('license::help.description_disclaimer') }}</span>


		<div class="widgets">

		@forelse($licenses as $license)

			<div class="widget c-widget license-box">

				
				<div>
					<div class="license-box__title">
						<h4>{{ $license->title }}</h4>
					
						
						<div style="flex-basis:160px;text-align:right">{!! $license->icon ?? '' !!}</div>
					</div>
					
					<x-markdown>{!! $license->description !!}</x-markdown>
						
					@if($license->license)
						<div><a href="{{ $license->license }}" target="_blank" rel="noopener noreferrer nofollow">{{ trans('administration.documentlicenses.view_license') }}</a></div>
					@endif
					
				</div>
			</div>

		@empty


		@endif

	</div>
	</div>

@stop

@section('footer')

	@include('footer')

@stop
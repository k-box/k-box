@extends('global')

@section('breadcrumbs')

	@if(isset($page_title))
		{{$page_title}}
	@elseif(isset($pagetitle))
		{{$pagetitle}}
	@endif

@stop


@section('content')

	<div class="c-page">

		<span class="description">{{ trans('license::help.description_disclaimer') }}</span>


		<div class="widgets">

		@forelse($licenses as $license)

			<div class="widget c-widget license-box">

				
				<div>
					<div class="license-box__title">
						<h4>{{ $license->title }}</h4>
					
						
						<div style="flex-basis:160px;text-align:right">{!! $license->icon ?? '' !!}</div>
					</div>
					<div>
						
						{!! Markdown::convertToHtml($license->description) !!}
					</div>
						
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
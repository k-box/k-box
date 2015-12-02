@extends('global')


@section('header')

	@include('headers.external')

@stop


@section('footer')

	@include('footer')

@stop


@section('content')

@if(!$file->canBePreviewed())

	<div class="disclaimer">

		<h4>{{trans('documents.preview.not_available')}}</h4>
	
	
		<a class="button button-primary" href="{{DmsRouting::document($document)}}" target="_blank" download="{{ $document->title }}">
			{{trans('panels.download_btn')}}
		</a>
	
	</div>

@else

	@if($type=='image')
	
		<img src="{{DmsRouting::document($document)}}" alt="{{$document->title}}">
	
	@elseif($type=='document' && $extension === 'pdf')
	
		<iframe src="{{DmsRouting::embed($document)}}" frameborder="0"></iframe>
	
	@elseif($extension === 'gdoc' || $extension === 'gslides' || $extension === 'gsheet' )
	
		<div class="disclaimer">
	
			@if(isset($render) && !empty($render))
	
				<h4>{{trans('documents.preview.google_file_disclaimer', ['document' => $document->title])}}</h4>
			
				<a class="button button-primary" href="{{$render}}" target="_blank">
					{{trans('documents.preview.open_in_google_drive_btn')}}
				</a>
			
			@else
			
				<h4>{{trans('documents.preview.error', ['document' => $document->title])}}</h4>
			
			@endif
		
		</div>
	
	@elseif(isset($render) && !empty($render))
	
		<div class="doc-page">
			{!!$render!!}
		</div>
	
	
	@else
	
		<div class="disclaimer">
	
			<h4>{{trans('documents.preview.error', ['document' => $document->title])}}</h4>
		
		
			<a class="button button-primary" href="{{DmsRouting::document($document)}}" download="{{ $document->title }}">
				{{trans('panels.download_btn')}}
			</a>
		
		</div>
	
	@endif

@endif

@stop
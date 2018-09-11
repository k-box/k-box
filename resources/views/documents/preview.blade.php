@extends('global')


@section('header')

@overwrite


@section('content')


	<div class="preview js-preview">
    
    <div class="preview__header">
	
		<a class="logo white" href="@if(isset( $is_user_logged ) && isset($current_user_home_route) && $is_user_logged){{$current_user_home_route}}@else{{route('frontpage')}}/@endif">
			@include('headers.logo')
		</a>
			

        <div class="preview__title-container">

            <span class="preview__title">{{ $document->title }}
			
				@if($version)
					&nbsp;/&nbsp;{{ $version->name }}
				@endif
			</span>            
        
        </div>

        <div class="preview__actions">
        
            <a class="preview__button js-preview-download-button"  href="{{DmsRouting::download($document, $version)}}" download="{{ $filename_for_download }}">{{ trans('panels.download_btn') }}</a>
            
            <button class="preview__button preview__button--expandable js-preview-details-button"><span class="preview__button-close">{{ trans('preview::actions.close') }}</span>{{ trans('preview::actions.details') }}</button>

        </div>
    
    </div>


    <div class="preview__body">

        <div class="preview__area js-preview-area">

            <div class="preview__content js-preview-content">

				@if(isset($preview_errors) && !is_null($preview_errors))

					<div class="disclaimer">

						<h4>{{ $preview_errors }}</h4>
					
					
						<a class="button button-primary" href="{{DmsRouting::download($document, $version)}}" target="_blank" download="{{ $filename_for_download }}">
							{{trans('panels.download_btn')}}
						</a>
					
					</div>

				@else

					{!! $previewable !!}

				@endif

{{-- 

	@if($type==\KBox\Documents\DocumentType::IMAGE)
	
		<img src="{{DmsRouting::download($document, $version)}}" alt="{{$document->title}}">

	@elseif($type==\KBox\Documents\DocumentType::VIDEO)

		@if($document->status === \KBox\DocumentDescriptor::STATUS_COMPLETED)

			<video id="the-player" 
				data-dash="{{ route('video.play', ['uuid' => $document->file->uuid, 'resource' => 'mpd']) }}"
				data-source="{{ DmsRouting::download($document, $version) }}"
				data-source-type="{{ $document->mime_type }}"
				controls preload="none"
				poster="{{ DmsRouting::thumbnail($document, $version) }}">

			</video>

		@else 

			<div class="c-message c-message--warning">
				<p>{{ trans('documents.preview.video_not_ready') }}</p>
			</div>

		@endif
	
	@elseif($type==\KBox\Documents\DocumentType::PDF_DOCUMENT)

		<iframe src="{{DmsRouting::embed($document, $version)}}" frameborder="0"></iframe>
	
	@elseif(isset($render) && !empty($render))
	
			{!!$render!!}
	
	@else
	
		<div class="preview__error">
	
			<h4>{{trans('documents.preview.error', ['document' => $document->title])}}</h4>
		
		
			<a class="button button-primary" href="{{DmsRouting::download($document, $version)}}" download="{{ $filename_for_download }}">
				{{trans('panels.download_btn')}}
			</a>
		
		</div>
	
	@endif

@endif --}}

            </div>
		</div>
		
        <div class="preview__sidebar js-preview-sidebar">
			@include('documents.partials.preview_properties')
        </div>
    </div>
</div>


</div>
	

@stop

@section('scripts')

	<script>
	require(['modules/preview'], function(Preview){

		Preview.load();
	});
	</script>

@stop


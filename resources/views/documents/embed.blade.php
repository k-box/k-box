@extends('global')


@section('header')

@overwrite


@section('content')

	<div class="preview js-preview">
    
    <div class="preview__header">
	
		<x-logo class="text-white" href="{{url('/')}}" target="_blank" rel="noopener noreferrer" />

        <div class="preview__title-container">

            <span class="preview__title">{{ $document->title }}
			
				@if($version)
					&nbsp;/&nbsp;{{ $version->name }}
				@endif
			</span>            
        
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

            </div>
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


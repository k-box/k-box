@extends('global')


@section('header')

@overwrite

@push('meta')
<link rel="alternate" type="application/json+oembed" href="{{ route('oembed', ['url' => DmsRouting::preview($document, $version), 'format' => 'json']) }}" title="{{ $document->title }}">
@endpush

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
		
			@auth
				@if(isset($user_can_edit) && $user_can_edit)
					<a class="preview__button js-preview-edit-button"  title="{{trans('panels.edit_btn_title')}}" href="{{ route('documents.edit', $document->id)}}">{{ trans('panels.edit_btn') }}</a>
				@endif
			@endauth
			
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


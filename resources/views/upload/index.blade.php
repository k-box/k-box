@extends('documents.document-layout')

@section('breadcrumbs')
		
    <a href="{{route('documents.index')}}"  class="breadcrumb__item">{{trans('documents.page_title')}}</a> <span class="breadcrumb__item--current">{{trans('documents.create.page_breadcrumb')}}</span>

@stop


@section('action-menu')


@stop


@section('content')

<div id="documents-list">

    <div id="document-area">

    @include('errors.list')
    
    @if(isset($target_error) && $target_error)
        <div class="c-message c-message--warning">
            {{ $target_error }}
        </div>
    @endif

	<h3>{{trans('actions.upload_alt')}} {!! $target !!}</h3>
    
    <p><em>{{ trans('upload.do_not_leave_the_page') }}</em></p>


    <div id="upload">
    </div>

    <div id="upload-template">

        <div class="upload-trigger js-upload-fallback"> 

            {{ trans('upload.action_drop') }} {{ trans('actions.or_alt') }} <button class="upload-trigger__button js-upload-fallback-button">{{ trans('upload.action_select') }}</button>

        </div>
    
    @verbatim

        <div class="uploads-list">

            {{#each uploadjobs}}

            <div class="upload" data-id="{{ id }}" >

                <div class="upload__title">

                    {{ metadata.filename }}
                    
                </div>

                <div class="progress">
                    <div class="widget--storage__meter">
                        <div class="bar" style="width:{{ uploadPercentage }}%"></div>
                    </div>
                </div>

                <div class="meta">

                    <span class="meta-info status">
                        {{status status }}
                    </span>


                    <span class="meta-info progress">
                        {{ uploadPercentage }}%
                    </span>
    @endverbatim    
                    <span class="meta-info">
                        @{{#if_eq status 1 }}
                        <button class="button" data-action="start" data-id="@{{ id }}">{{ trans('upload.start') }}</button>
                        @{{/if_eq }}
                        @{{#if_leq status 3 }}
                        <button class="button" data-action="cancel" data-id="@{{ id }}">{{ trans('upload.cancel') }}</button>
                        @{{/if_leq }}
                        @{{#if_eq status 4 }}
                        <button class="button" data-action="open" data-id="@{{ id }}">{{ trans('upload.open_file_location') }}</button>
                        @{{/if_eq }}
                    </span>
                    
                </div>

            </div>
    @verbatim    

            {{/each}}
        
        </div>

    @endverbatim
    </div>

</div>
</div>

@stop

@section('panels')

@include('panels.generic')

@stop

@section('scripts')

	<script>
        require(["modules/uploadjobs"], function(UploadJobs){
            $('.dz-message').hide();

            @if($target_collection)

                UploadJobs.setTargetCollection('{{$target_collection}}');

            @endif

        });
	</script>

@stop
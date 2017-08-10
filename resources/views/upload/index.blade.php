@extends('documents.document-layout')

@section('breadcrumbs')
		
    <a href="{{route('documents.index')}}"  class="breadcrumb__item">{{trans('documents.page_title')}}</a> <span class="breadcrumb__item--current">{{trans('documents.create.page_breadcrumb')}}</span>

@stop


@section('action-menu')


@stop


@section('document_list_area')

    @include('errors.list')

	<h3>{{trans('documents.upload.page_title')}}</h3>

    @verbatim

    <div id="upload">
    </div>

    <div id="upload-template">

        <div class="upload-trigger"> 

            <label for="file">Drop here a file or <button class="upload-trigger__button">Select it</button></label>

            <input type="file" class="upload-field" name="file" id="file">

        </div>
    

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
                    
                    <span class="meta-info">
                        {{#if_eq status 1 }}
                        <button class="button" data-action="start" data-id="{{ id }}" >Start</button>
                        {{/if_eq }}
                        {{#if_geq status 2 }}
                        <button class="button" data-action="cancel" data-id="{{ id }}" >Cancel</button>
                        {{/if_geq }}
                        <button class="button" data-action="remove" data-id="{{ id }}">Remove</button>
                    </span>
                    
                </div>

            </div>

            {{/each}}
        
        </div>

    </div>

    @endverbatim
    

@stop

@section('panels')

@include('panels.generic')

@stop

@section('scripts')

	<script>
        require(["modules/uploadjobs"], function(UploadJobs){
            $('.dz-message').hide();
        });
	</script>

@stop

@extends('management-layout')

@section('sub-header')
        
    <a href="{{route('documents.index')}}" class="parent">{{trans('documents.page_title')}}</a> {{trans('import.page_title')}}

@stop

@section('action-menu')

    

        <a href="#" class="button disabled" rv-on-click="clearCompleted" rv-disabled="cannotClear">{{trans('import.clear_completed_btn')}}</a>

    

@stop

@section('content')

    <div id="import">

        <div class="row">
            @include('import.importform')
        </div>

        <div class="row">

            <div class="widget import-status @if($imports_total != $imports_completed) visible @endif" id="import-status" data-bind="status" rv-visible="isImporting">
            
            <p class="global" rv-text="status.global">{{$status['global']}}</p>

            <p class="details" rv-text="status.details">{!! $status['details'] !!}</p>

            <div class="meter">
                <div class="bar" rv-width="status.progress_percentage" style="width:{{$status['progress_percentage']}} "></div>
            </div>

            </div>


        </div>

    	<div class="row" id="status">

            <div class="list details import-list" id="import-list">

                <div>

                    <div rv-template="imports">

                        {% _.forEach(elements, function(el) { %}

                        <div class="item import-{# el.status_message #}" data-id="00" data-file-id="999" >

                            <div class="icon">
                                <span class="status-icon icon-action-black status-{# el.status_message #}"></span>

                                {% if(el.is_remote){ %}<span class="origin-icon icon-action-black icon-action-black-ic_language_black_24dp"></span> {% } %}

                                {% if(!el.is_remote){ %}<span class="origin-icon icon-action-black"></span> {% } %}

                            </div>

                            <h2 class="title">

                                {# el.file.name #}

                                <span class="comment origin">{# el.file.original_uri #}</span>
                                
                            </h2>

                            <div class="meta">

                                <span class="meta-info creation-date">
                                    <span class="meta-label">{{trans('documents.descriptor.added_on')}}&nbsp;</span>{# el.created_at #}
                                </span>

                                <span class="meta-info status">
                                    {# el.status_message #}
                                </span>

                                <span class="meta-info document-type">
                                    {#  el.file.mime_type #}
                                </span>

                                <span class="meta-info progress">
                                     {# el.bytes_expected==null ? '0' : el.bytes_expected===0 ? "0" : Math.round(el.bytes_received*100/el.bytes_expected) #}%
                                </span>
                                
                            </div>

                        </div>

                        {% }); %}
                    </div>
                
                </div>

                <div class="cache">
                    @foreach($imports as $import)

                        @include('documents.partials.importsingle', ['item' => $import])

                    @endforeach
                </div>

            </div>
	   </div>
    </div>


@stop


@section('scripts')

	<script>
            
	require(['modules/import'], function(Import){

        @if($imports_total != $imports_completed)

            Import.startUiUpdate("{{$status['global']}}", "{!! $status['details'] !!}", "{{$status['progress_percentage']}}");

        @endif

        @if($imports_completed > 0)

            Import.enableClearCompleted();

        @endif

	});
	
	</script>

@stop

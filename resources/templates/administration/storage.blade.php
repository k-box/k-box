@extends('management-layout')

@section('sub-header')
        
    <a href="{{route('administration.index')}}" class="parent">{{trans('administration.page_title')}}</a> {{trans('administration.menu.storage')}}

@stop

@section('action-menu')


<div class="action-group" id="storageActions">
    <a href="{{ route('administration.storage.reindexall') }}" rv-on-click="reindexAll" rv-disabled="cannotReindex" class="button">
        <span class="btn-icon icon-action-white icon-action-white-ic_cached_white_24dp"></span> {{ trans('administration.storage.reindexall_btn') }}
    </a>
</div>


@stop

@section('content')

<div class="row">

    <div class="two columns">

        @include('administration.adminmenu', ['small' => true, 'compact' => true])

    </div>

    <div class="six columns ">


        <div id="reindex">

            

            <div class="widget widget-reindex hideable @if(!is_null($reindex)) visible @endif" id="import-status" rv-visible="isReindexing">
            
                <p class="global" rv-text="status.status">{{ $reindex['status'] }}</p>


                <div class="meter">
                    <div class="bar" rv-width="status.progress_percentage" style="width:{{$reindex['progress_percentage']}}%"></div>
                </div>

            </div>

            

        </div>


        <div class="widget">

            <form  method="post" action="{{route('administration.storage.naming')}}">

                <input type="hidden" name="_token" value="{{{ csrf_token() }}}"> 

                <h3>{{trans('administration.storage.naming_policy_title')}}</h3>

                <p class="description">{{trans('administration.storage.naming_policy_description')}}</p>

                <p>


                    <pre><code>date_title_authors_language_version</code></pre>

                    <ul>
                        <li><code>date</code>: YYYY-MM-DD</li>
                        <li><code>title</code>: alpha numeric without spaces (spaces must be encoded with dashes, no underscore or characters like ? ! $ % & #)</li>
                        <li><code>authors</code>: alphanumeric without spaces (author1_name-author1_surname--author2_name-author2_surname)</li>
                        <li><code>language</code>: two letter language code (e.g. en, ru, kg)</li>
                        <li><code>version</code>: number with a maximum of three digits</li>
                    </ul>
                </p>

                
                <p>

                @if($is_naming_policy_active)

                    <input type="hidden" value="0" name="activate">

                    <button type="submit" class="button">
{{--                         {{trans('administration.storage.naming_policy_btn_save')}} --}}
                        {{trans('administration.storage.naming_policy_btn_deactivate')}}
                    </button>

                @else

                    <input type="hidden" value="1" name="activate">

                    <button type="submit" class="button button-primary">
                        {{trans('administration.storage.naming_policy_btn_activate')}}
                    </button>

                @endif

                </p>

            </form>

        </div>


        <div class="widget">

            <h3>{{trans('administration.storage.disk_status_title')}}</h3>

            @foreach($disks as $disk)

                <div class="card">

                    <strong><span class="card-icon icon-device-black icon-device-black-ic_sd_storage_black_24dp"></span> {{$disk['name']}} {{$disk['type']}}</strong>

                    <p>

                        {!! trans('administration.storage.disk_space', ['free' => $disk['free'], 'used' => $disk['used'], 'total' => $disk['total']]) !!}

                    </p>


                    <div class="meter">
                        <div class="bar {{$disk['level']}}" style="width:{{$disk['used_percentage'] }}% "></div>
                    </div>

                </div>

            @endforeach

        </div>


    </div>

    <div class="four columns">

        <div class="widget storage-statistics">
        
            <h5 class="widget-title"><span class="widget-icon icon-action-black icon-action-black-ic_description_black_24dp"></span> {{trans('administration.storage.documents_report_title')}}</h5>

            <div class="document">
        
            @foreach($status['document_categories'] as $key => $values)

                @if($values['total'] > 0)
                    <p><strong>{{$values['total']}}</strong> {{trans_choice('documents.type.' . $key, $values['total'])}}</p>
                @endif

            @endforeach

            </div>

        </div>

    </div>

</div>

@stop

@section('scripts')

    <script>
    require(['modules/admin-storage'], function(Storage){

        @if(!is_null($reindex) && $reindex['total'] > 0)

            Storage.startUiUpdate('{{ $reindex['status'] }}', {{$reindex['progress_percentage']}});

        @endif
        
    });
    </script>

@stop
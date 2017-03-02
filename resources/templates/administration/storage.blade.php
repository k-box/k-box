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

        @include('administration.adminmenu')

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

    </div>

    <div class="four columns widgets">

        @if(isset($storage))

        <div class="c-widget widget--storage">

            <span class="widget--storage__percentage">{{ trans('widgets.storage.used_percentage', ['used' => $storage['percentage']]) }}</span>

            <span class="widget--storage__space">{{ trans('widgets.storage.used_alt', ['used' => $storage['used'], 'total' => $storage['total']]) }}</span>

            <div class="widget--storage__multimeter">
                @foreach($storage['graph'] as $key => $graph)
                    <div class="bar force-bar-normal-state" title="{{$graph['label']}}" data-series="{{$key}}" style="width:{{$graph['value'] }}%;background-color:#{{$graph['color']}}"></div>
                @endforeach
            </div>

            <div class="widget--storage__legend">
                @foreach($storage['graph'] as $key => $graph)
                    <div class="legend-entry">
                        <span class="legend-sign" data-series="{{$key}}" style="background-color:#{{$graph['color']}}"></span> {{$graph['label']}}
                    </div>
                @endforeach
            </div>

        </div>

        @endif

    </div>

</div>

@stop

@section('scripts')

    <script>
    require(['modules/admin-storage'], function(Storage){

        @if(!is_null($reindex) && $reindex['total'] > 0)

            Storage.startUiUpdate('{{ $reindex['status'] }}', {{$reindex['progress_percentage']}});

        @endif

        $('.force-bar-normal-state').removeClass('force-bar-normal-state');
        
    });
    </script>

@stop
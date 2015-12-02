@extends('management-layout')

@section('sub-header')
        
    <a href="{{route('administration.index')}}" class="parent">{{trans('administration.page_title')}}</a> {{$pagetitle}}

@stop

@section('action-menu')


<!--<div class="action-group">
    <a href="{{ route('administration.institutions.create') }}" class="button">
        <span class="btn-icon icon-content-white icon-content-white-ic_add_circle_outline_white_24dp"></span>{{trans('administration.institutions.create_institutions_btn')}}
    </a>
    
</div>-->


@stop

@section('content')

<div class="row">

    <div class="two columns">

        @include('administration.adminmenu', ['small' => true, 'compact' => true])

    </div>

    <div class="ten columns ">
        
        @include('errors.list')

        <div class="widget">

            <form  method="post" action="{{route('administration.settings.store')}}">
    
                <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
                
                
                
                <h3>{{trans('administration.settings.viewing_section')}}</h3>
    
                <p class="description">{{trans('administration.settings.viewing_section_help')}}</p> 
            
                <p>                
                    <input type="checkbox" name="map_visualization" id="map_visualization" value="true" @if(isset($map_visualization) && $map_visualization) checked @endif /><label for="map_visualization">{{trans('administration.settings.map_visualization_chk')}}</label>
                </p>
                
                <button type="submit" class="button">
                    {{trans('administration.settings.save_btn')}}
                </button>
            
            </form>
        
        </div>
        
        
        <div class="widget">

            <form  method="post" action="{{route('administration.settings.store')}}">
    
                <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
                
                
                
                <h3>{{trans('administration.settings.klinkpublic_section')}}</h3>
    
                <p class="description">{{trans('administration.settings.klinkpublic_section_help')}}</p> 
            
                <p>                
                    <input type="checkbox" name="public_core_enabled" id="public_core_enabled" value="true" @if(isset($public_core_enabled) && $public_core_enabled) checked @endif /><label for="public_core_enabled">{{trans('administration.settings.klinkpublic_enabled')}}</label>
                </p>

                <p>
                    
                    <label for="public_core_url">{{trans('administration.settings.klinkpublic_url')}}</label>
                    @if( $errors->has('public_core_url') )
                        <span class="field-error">{{ implode(",", $errors->get('public_core_url'))  }}</span>
                    @endif
                    <input type="text" required name="public_core_url" id="public_core_url" value="{{old('public_core_url', isset($public_core_url) ? $public_core_url : '')}}">

                    <label for="public_core_username">{{trans('administration.settings.klinkpublic_username')}}</label>
                    @if( $errors->has('public_core_username') )
                        <span class="field-error">{{ implode(",", $errors->get('public_core_username'))  }}</span>
                    @endif
                    <input type="text" required name="public_core_username" id="public_core_username" value="{{old('public_core_username', isset($public_core_username) ? $public_core_username : '')}}">

                    <label for="public_core_password">{{trans('administration.settings.klinkpublic_password')}}</label>
                    @if( $errors->has('public_core_password') )
                        <span class="field-error">{{ implode(",", $errors->get('public_core_password'))  }}</span>
                    @endif
                    <input type="password" required name="public_core_password" id="public_core_password" value="{{old('public_core_password', isset($public_core_password) ? $public_core_password : '')}}">
                </p>
                
                
                <p>                
                    <input type="checkbox" name="public_core_debug" id="public_core_debug" value="true" @if(isset($public_core_debug) && $public_core_debug) checked @endif /><label for="public_core_debug">{{trans('administration.settings.klinkpublic_debug_enabled')}}</label>
                </p>
                
                <button type="submit" class="button">
                    {{trans('administration.settings.save_btn')}}
                </button>
            
            </form>
        
        </div>

    </div>

</div>

@stop
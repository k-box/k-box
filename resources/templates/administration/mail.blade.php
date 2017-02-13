@extends('management-layout')

@section('sub-header')
        
    <a href="{{route('administration.index')}}" class="parent">{{trans('administration.page_title')}}</a> {{trans('administration.menu.mail')}}

@stop

@section('action-menu')


<div class="action-group">
    <a href="{{ route('administration.mail.test') }}" class="button">
        <span class="btn-icon icon-content-white icon-content-white-ic_mail_white_24dp"></span>{{trans('administration.mail.test_btn')}}
    </a>
</div>


@stop

@section('content')

<div class="row">

    <div class="two columns">

        @include('administration.adminmenu', ['small' => true, 'compact' => true])

    </div>

    <div class="ten columns ">

        @if( $errors->has('mail_send') )
            <div class="alert error">

                <p>{{implode(",", $errors->get('mail_send'))}}</p>

            </div>
        @else

            @include('errors.list')

        @endif


        <form method="post" action="{{route('administration.mail.store')}}">

            <input type="hidden" name="_token" value="{{{ csrf_token() }}}"> 

            <div class="row">
                
                @if( $errors->has('pretend') )
                    <span class="field-error">{{ implode(",", $errors->get('pretend'))  }}</span>
                @endif
                <input type="checkbox" name="pretend" id="pretend" value="1" @if(isset($config['pretend']) && !$config['pretend']) checked="true" @endif /><label for="pretend">{{trans('administration.mail.enable_chk')}}</label>
            </div>
        

            <div class="row">
                <label>{{trans('administration.mail.from_label')}}</label>
                <p class="description">{{trans('administration.mail.from_description')}}</p>
                @if( $errors->has('from_address') )
                    <span class="field-error">{{ implode(",", $errors->get('from_address'))  }}</span>
                @endif
                
                <input type="email" name="from_address" value="@if(isset($config['from']['address'])){{$config['from']['address']}}@endif" placeholder="{{trans('administration.mail.from_address_placeholder')}}" />
                @if( $errors->has('from_name') )
                    <span class="field-error">{{ implode(",", $errors->get('from_name'))  }}</span>
                @endif
                <input type="text" name="from_name" value="@if(isset($config['from']['name'])){{$config['from']['name']}}@endif" placeholder="{{trans('administration.mail.from_name_placeholder')}}" />
            </div>

            <div class="row">
                <label>{{trans('administration.mail.host_label')}}</label>
                @if( $errors->has('host') )
                    <span class="field-error">{{ implode(",", $errors->get('host'))  }}</span>
                @endif
                <input type="text" name="host" required value="{{$config['host']}}" />
            </div>
            <div class="row">
                <label>{{trans('administration.mail.port_label')}}</label>
                @if( $errors->has('port') )
                    <span class="field-error">{{ implode(",", $errors->get('port'))  }}</span>
                @endif
                <input type="number" name="port" required value="{{$config['port']}}" />
            </div>
            
            <div class="row">
                <label>{{trans('administration.mail.encryption_label')}}</label>
                @if( $errors->has('encryption') )
                    <span class="field-error">{{ implode(",", $errors->get('encryption'))  }}</span>
                @endif
                <input type="text" disabled name="encryption" value="{{$config['encryption']}}"  />
            </div>

            <div class="row">
                <label>{{trans('administration.mail.username_label')}}</label>
                @if( $errors->has('smtp_u') )
                    <span class="field-error">{{ implode(",", $errors->get('smtp_u'))  }}</span>
                @endif
                <input type="text" name="smtp_u" value="{{$config['username']}}"  />
            </div>

            <div class="row">
                <label>{{trans('administration.mail.password_label')}}</label>
                @if( $errors->has('smtp_p') )
                    <span class="field-error">{{ implode(",", $errors->get('smtp_p'))  }}</span>
                @endif
                <input type="password" name="smtp_p" value="{{$config['password']}}"  />
            </div>

            <div class="row">

                <button class="button button-primary" type="submit">{{trans('administration.mail.save_btn')}}</button>

            </div>

        </form>
        

    </div>

</div>

@stop
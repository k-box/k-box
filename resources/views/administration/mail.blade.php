@extends('administration.layout')

@section('breadcrumbs')
        
    <a href="{{route('administration.index')}}"  class="breadcrumb__item">{{trans('administration.page_title')}}</a> <span class="breadcrumb__item--current">{{trans('administration.menu.mail')}}</span>

@stop

@section('action-menu')


<a href="{{ route('administration.mail.test') }}" class="action__button">
    @materialicon('content', 'mail'){{trans('administration.mail.test_btn')}}
</a>


@stop

@section('page')

        @include('dashboard.notices')

        @if( $errors->has('mail_send') )
            <div class="c-message c-message--error">

                <p>{{implode(",", $errors->get('mail_send'))}}</p>

            </div>
        @else

            @include('errors.list')

        @endif


        <form method="post" class="c-form" action="{{route('administration.mail.store')}}">

            {{ csrf_field() }}

            <div class="c-section">
                <h4 class="c-section__title">{{trans('administration.mail.from_label')}}</h4>
                <p class="c-section__description">{{trans('administration.mail.from_description')}}</p>

                <div class="c-form__field">

                    <label for="from_address">{{ trans('administration.mail.from_address') }}</label>

                    @if( $errors->has('from_address') )
                        <span class="field-error">{{ implode(",", $errors->get('from_address'))  }}</span>
                    @endif
                    
                    <input class="c-form__input" type="email" name="from_address" id="from_address" required value="@if(isset($config['from']['address'])){{$config['from']['address']}}@endif" placeholder="{{trans('administration.mail.from_address_placeholder')}}" />
                
                </div>



                <div class="c-form__field">

                    <label for="from_name">{{ trans('administration.mail.from_name') }}</label>
                    @if( $errors->has('from_name') )
                        <span class="field-error">{{ implode(",", $errors->get('from_name'))  }}</span>
                    @endif
                    <input class="c-form__input" type="text" name="from_name" id="from_name" value="@if(isset($config['from']['name'])){{$config['from']['name']}}@endif" placeholder="{{trans('administration.mail.from_name_placeholder')}}" />

                </div>
            </div>
            
            <div class="c-section">
                <h4 class="c-section__title">{{trans('administration.mail.server_configuration_label')}}</h4>
                <p class="c-section__description">{{trans('administration.mail.server_configuration_description')}}. <strong>{{trans('administration.mail.encryption_label')}}</strong></p>

                @if(!$is_server_configurable)
                
                    <div class="c-form__blocked-reason c-message">
                        {{ trans('administration.mail.log_driver_used') }}<br/>
                        {!! trans('administration.mail.log_driver_go_to_log', ['link' => route('administration.maintenance.index')]) !!}
                    </div>
                
                @endif

                <div class="c-form__field  @if(!$is_server_configurable) c-form--blocked @endif">
                    <label>{{trans('administration.mail.host_label')}}</label>
                    @if( $errors->has('host') )
                        <span class="field-error">{{ implode(",", $errors->get('host'))  }}</span>
                    @endif
                    <input class="c-form__input" type="text" name="host" @if($is_server_configurable) required @endif value="{{$config['host']}}" />
                </div>
                
                <div class="c-form__field  @if(!$is_server_configurable) c-form--blocked @endif">
                    <label>{{trans('administration.mail.port_label')}}</label>
                    @if( $errors->has('port') )
                        <span class="field-error">{{ implode(",", $errors->get('port'))  }}</span>
                    @endif
                    <input class="c-form__input" type="number" class="c-input--number" name="port" @if($is_server_configurable) required @endif value="{{$config['port']}}" />
                </div>
                
                <div class="c-form__field  @if(!$is_server_configurable) c-form--blocked @endif">
                    <label>{{trans('administration.mail.username_label')}}</label>
                    @if( $errors->has('smtp_u') )
                        <span class="field-error">{{ implode(",", $errors->get('smtp_u'))  }}</span>
                    @endif
                    <input class="c-form__input" type="text" name="smtp_u" value="{{$config['username']}}"  />
                </div>

                <div class="c-form__field  @if(!$is_server_configurable) c-form--blocked @endif">
                    <label>{{trans('administration.mail.password_label')}}</label>
                    @if( $errors->has('smtp_p') )
                        <span class="field-error">{{ implode(",", $errors->get('smtp_p'))  }}</span>
                    @endif
                    <input class="c-form__input" type="password" name="smtp_p" value="{{$config['password']}}"  />
                </div>

            </div>


            


            <div class="c-form__buttons">

                <button class="button button-primary" type="submit">{{trans('administration.mail.save_btn')}}</button>

            </div>

        </form>

@stop
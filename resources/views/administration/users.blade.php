@extends('administration.layout')

@section('breadcrumbs')
        
    <a href="{{route('administration.index')}}" class="breadcrumb__item">{{trans('administration.page_title')}}</a>
    <span class="breadcrumb__item--current">{{trans('administration.menu.accounts')}}</span>

@endsection

@section('action-menu')

<a href="{{ route('administration.users.create') }}" class="action__button">
    @materialicon('social', 'person_add', 'inline-block'){{trans('administration.accounts.create_user_btn')}}
</a>
    
<div class="separator"></div>
    
<a href="{{ route('administration.messages.create') }}" class="action__button" title="{{trans('administration.accounts.send_message_btn_hint')}}">
    @materialicon('content', 'send', 'inline-block'){{trans('administration.accounts.send_message_btn')}}
</a>

@stop

@section('page')

        @include('dashboard.notices')
        
        @include('errors.list')

        <table class="c-table">
            <thead class="c-table__head">
                <tr>
                    <th style="width:20%">{{trans('administration.accounts.table.name_column')}}</th>
                    <th style="width:30%">{{trans('administration.accounts.table.email_column')}}</th>
                    <th style="width:24%">{{trans('administration.accounts.table.institution_column')}}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>

                @foreach($users as $user)
                    <tr @if ($user->trashed()) class="trashed" @endif>
                        <td>
                            @component('avatar.full', ['image' => $user->avatar, 'name' => $user->name])

                                {{$user->name}}

                            @endcomponent
                        </td>
                        <td><a href="{{ route('administration.users.show', $user->id) }}">{{$user->email}}</a></td>
                        <td>{{$user->getInstitutionName()}}</td>
                        <td>

                            @if ($user->trashed())
                                <a class="button " href="{{ route('administration.users.restore', $user->id) }}">
                                @materialicon('content', 'undo', 'inline-block'){{trans('actions.restore')}}</a>
                            @else
                                <a class="button" href="{{ route('administration.users.edit', $user->id) }}">@materialicon('content','create'){{trans('actions.edit')}}</a>

                                @if(isset($current_user) && $current_user!= $user->id)
                                    <a class="button button--danger" href="{{ route('administration.users.remove', $user->id) }}" onclick="if(!confirm('{{trans('administration.accounts.disable_confirm', ['name' => $user->name])}}?')) return false;">@materialicon('content', 'block'){{trans('actions.disable')}}</a>
                                @endif
                                
                                <a class="button" href="{{ route('administration.users.resetpassword', $user->id) }}" title="{{trans('administration.accounts.send_reset_password_hint')}}"><span class="btn-icon icon-content-black icon-content-black-ic_send_black_24dp"></span>{{trans('administration.accounts.send_reset_password_btn')}}</a>
                            @endif


                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

@stop
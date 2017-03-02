@extends('management-layout')

@section('sub-header')
        
    <a href="{{route('administration.index')}}" class="parent">{{trans('administration.page_title')}}</a> {{trans('administration.menu.accounts')}}

@stop

@section('action-menu')


<div class="action-group">
    <a href="{{ route('administration.users.create') }}" class="button">
        <span class="btn-icon icon-social-white icon-social-white-ic_person_add_white_24dp"></span>{{trans('administration.accounts.create_user_btn')}}
    </a>
    
    <div class="separator"></div>
    
    <a href="{{ route('administration.messages.create') }}" class="button" title="{{trans('administration.accounts.send_message_btn_hint')}}">
        <span class="btn-icon icon-content-white icon-content-white-ic_send_white_24dp"></span>{{trans('administration.accounts.send_message_btn')}}
    </a>
    
</div>


@stop

@section('content')

<div class="row">

    <div class="two columns">

        @include('administration.adminmenu')

    </div>

    <div class="ten columns ">

        @include('dashboard.notices')
        
        @include('errors.list')

        <table>
            <thead>
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
                            @include('avatar.picture', ['image' => $user->avatar, 'inline' => true, 'user_name' => $user->name, 'no_link' => true])
                            {{$user->name}}
                        </td>
                        <td><a href="{{ route('administration.users.show', $user->id) }}">{{$user->email}}</a></td>
                        <td>{{$user->getInstitutionName()}}</td>
                        <td>

                            @if ($user->trashed())
                                <a class="button " href="{{ route('administration.users.restore', $user->id) }}">
                                <span class="btn-icon icon-content-black icon-content-black-ic_undo_black_24dp"></span>{{trans('actions.restore')}}</a>
                            @else
                                <a class="button" href="{{ route('administration.users.edit', $user->id) }}"><span class="btn-icon icon-content-black icon-content-black-ic_create_black_24dp"></span>{{trans('actions.edit')}}</a>

                                @if(isset($current_user) && $current_user!= $user->id)
                                    <a class="button danger" href="{{ route('administration.users.remove', $user->id) }}" onclick="if(!confirm('{{trans('administration.accounts.disable_confirm', ['name' => $user->name])}}?')) return false;"><span class="btn-icon icon-content-black icon-content-black-ic_block_black_24dp"></span>{{trans('actions.disable')}}</a>
                                @endif
                                
                                <a class="button" href="{{ route('administration.users.resetpassword', $user->id) }}" title="{{trans('administration.accounts.send_reset_password_hint')}}"><span class="btn-icon icon-content-black icon-content-black-ic_send_black_24dp"></span>{{trans('administration.accounts.send_reset_password_btn')}}</a>
                            @endif


                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</div>

@stop
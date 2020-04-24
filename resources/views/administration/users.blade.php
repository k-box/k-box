@extends('administration.layout')

@section('breadcrumbs')
        
    <a href="{{route('administration.index')}}" class="breadcrumb__item">{{trans('administration.page_title')}}</a>
    <span class="breadcrumb__item--current">{{trans('administration.menu.accounts')}}</span>

@endsection

@section('action-menu')

<a href="{{ route('administration.users.create') }}" class="button mr-2">
    @materialicon('social', 'person_add', 'inline-block mr-1'){{trans('administration.accounts.create_user_btn')}}
</a>
    
<a href="{{ route('administration.messages.create') }}" class="button" title="{{trans('administration.accounts.send_message_btn_hint')}}">
    @materialicon('content', 'send', 'inline-block mr-1'){{trans('administration.accounts.send_message_btn')}}
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
                    <th style="width:24%">&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>

                @foreach($users as $user)
                    <tr @if ($user->trashed()) class="text-gray-500" @endif>
                        <td>
                            @component('avatar.full', ['image' => $user->avatar, 'name' => $user->name])

                                @if ($user->trashed())
                                    {{$user->name}}
                                @else
                                    <a href="{{ route('administration.users.show', $user->id) }}">{{$user->name}}</a>
                                @endif

                            @endcomponent
                        </td>
                        <td>{{$user->email}}</td>
                        <td>&nbsp;</td>
                        <td>

                            @if ($user->trashed())
                                <a class="button " href="{{ route('administration.users.restore', $user->id) }}">
                                @materialicon('content', 'undo', 'inline-block'){{trans('actions.restore')}}</a>
                            @else
                                <a class="button" href="{{ route('administration.users.edit', $user->id) }}">@materialicon('content','create', 'w-5 h-5 mr-1'){{trans('actions.edit')}}</a>

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
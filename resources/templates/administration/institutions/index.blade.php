@extends('management-layout')

@section('sub-header')
        
    <a href="{{route('administration.index')}}" class="parent">{{trans('administration.page_title')}}</a> {{$pagetitle}}

@stop

@section('action-menu')


<div class="action-group">
    <a href="{{ route('administration.institutions.create') }}" class="button">
        <span class="btn-icon icon-content-white icon-content-white-ic_add_circle_outline_white_24dp"></span>{{trans('administration.institutions.create_institutions_btn')}}
    </a>
    
</div>


@stop

@section('content')

<div class="row">

    <div class="two columns">

        @include('administration.adminmenu', ['small' => true, 'compact' => true])

    </div>

    <div class="ten columns ">
        
        @include('errors.list')

        <table>
            <thead>
                <tr>
                    <th style="width:40%">{{trans('administration.accounts.table.name_column')}}</th>
                    <th style="width:30%">{{trans('administration.accounts.table.email_column')}}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>

                @foreach($institutions as $institution)
                    <tr>
                        <td>
                            @include('avatar.picture', ['inline' => true, 'user_name' => $institution->klink_id, 'no_link' => true])
                            <a href="{{ route('administration.institutions.show', $institution->id) }}">{{$institution->name}}</a>
                        </td>
                        <td>{{$institution->email}}</td>
                        <td>
                            
                            <form action="{{ route('administration.institutions.destroy', $institution->id) }}" onsubmit="if(!confirm('{{trans('administration.institutions.delete_confirm', ['name' => $institution->name])}}')) return false;" method="POST">
                                
                                <a class="button" href="{{ route('administration.institutions.edit', $institution->id) }}"><span class="btn-icon icon-content-black icon-content-black-ic_create_black_24dp"></span>{{trans('actions.edit')}}</a>
                                
                                @if(isset($current_institution) && $current_institution != $institution->klink_id)
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
                                    <button type="submit" class="button danger"><span class="btn-icon icon-action-black icon-action-black-ic_delete_black_24dp"></span>{{trans('actions.trash_btn')}}</button>
                                @endif
                            </form>
                            

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</div>

@stop
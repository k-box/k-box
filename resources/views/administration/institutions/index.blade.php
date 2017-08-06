@extends('administration.layout')

@section('breadcrumbs')
        
    <a href="{{route('administration.index')}}"  class="breadcrumb__item">{{trans('administration.page_title')}}</a> <span class="breadcrumb__item--current">{{$pagetitle}}</span>

@stop

@section('action-menu')

<a href="{{ route('administration.institutions.create') }}" class="action__button">
    @materialicon('content', 'add_circle_outline'){{trans('administration.institutions.create_institutions_btn')}}
</a>


@stop

@section('page')
        
        @include('errors.list')

        <table class="c-table">
            <thead class="c-table__head">
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
                            <a href="{{ route('administration.institutions.show', $institution->id) }}">{{$institution->name}}</a>
                        </td>
                        <td>{{$institution->email}}</td>
                        <td>
                            
                            <form action="{{ route('administration.institutions.destroy', $institution->id) }}" onsubmit="if(!confirm('{{trans('administration.institutions.delete_confirm', ['name' => $institution->name])}}')) return false;" method="POST">
                                
                                <a class="button" href="{{ route('administration.institutions.edit', $institution->id) }}">@materialicon('content','create'){{trans('actions.edit')}}</a>
                                
                                @if(isset($current_institution) && $current_institution != $institution->klink_id)
                                    <input type="hidden" name="_method" value="DELETE">
                                    {{ csrf_field() }}
                                    <button type="submit" class="button danger">@materialicon('action', 'delete'){{trans('actions.trash_btn')}}</button>
                                @endif
                            </form>
                            

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

@stop
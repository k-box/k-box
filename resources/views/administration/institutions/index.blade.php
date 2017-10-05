@extends('administration.layout')

@section('breadcrumbs')
        
    <a href="{{route('administration.index')}}"  class="breadcrumb__item">{{trans('administration.page_title')}}</a> <span class="breadcrumb__item--current">{{$pagetitle}}</span>

@stop

@section('page')
        
        @include('errors.list')

        <div class="c-message c-message--warning">
            {{ trans('administration.institutions.deprecated') }}
        </div>

        <table class="c-table">
            <thead class="c-table__head">
                <tr>
                    <th style="width:40%">{{trans('administration.accounts.table.name_column')}}</th>
                    <th style="width:30%">{{trans('administration.accounts.table.email_column')}}</th>
                </tr>
            </thead>
            <tbody>

                @foreach($institutions as $institution)
                    <tr>
                        <td>
                            <a href="{{ route('administration.institutions.show', $institution->id) }}">{{$institution->name}}</a>
                        </td>
                        <td>{{$institution->email}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

@stop
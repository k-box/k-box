@extends('management-layout')

@section('sub-header')
        
    <a href="{{route('administration.index')}}" class="parent">{{trans('administration.page_title')}}</a> {{trans('administration.menu.language')}}

@stop

@section('action-menu')


<div class="action-group">

</div>


@stop

@section('content')

<div class="row">

    <div class="two columns">

        @include('administration.adminmenu')

    </div>

    <div class="ten columns ">

        <h5>{{trans('administration.language.list_label')}}</h5>

        <table>
            <thead>
                <tr>
                    <th style="width:16%">{{trans('administration.language.code_column')}}</th>
                    <th style="">{{trans('administration.language.name_column')}}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>

                @foreach($languages as $language)
                    <tr>
                        <td>
                            {{$language}}
                        </td>
                        <td>
                            {{trans('languages.' . $language)}}
                        </td>
                        <td>
                        
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</div>

@stop
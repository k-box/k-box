@extends('global')

@section('content')

<div class="sidebar">

    @include('administration.adminmenu')

</div>

<div class="sidebar__spaced">

    @if(Session::has('flash_message'))

        <div class="c-message c-message--success">
            {{session('flash_message')}}
        </div>

    @endif

    @yield('page')

</div>

@stop
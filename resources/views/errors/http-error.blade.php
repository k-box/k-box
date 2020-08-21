@extends('layout.full-form')

@section('content')

    <div class="text-3xl max-w-md">@yield('message')</div>

    @section('actions')
        <div class="mt-8">
            <a class="button" href="{{ redirect()->back()->getTargetUrl() }}">{{ trans('errors.go_back_btn') }}</a>
        </div>
    @show

@endsection

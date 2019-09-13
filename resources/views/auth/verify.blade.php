@extends('layout.login')

@push('title')
    {{ trans('mail.verify.verify_email') }} &ndash; 
@endpush

@section('form')

    <div class="c-form c-form--space">
        <h2 class="mb-1">{{ trans('mail.verify.verify_email') }}</h2>

        <div class=" mb-4">
            @if (session('resent'))
                <div class="alert success" role="alert">
                    {{ trans('mail.verify.email_resent') }}
                </div>
            @endif

            <p class="mb-2">
                {{ trans('mail.verify.before_proceeding') }}
            </p>
            <p>
                {{ trans('mail.verify.if_not_received') }}, <a href="{{ route('verification.resend') }}">{{ trans('mail.verify.request_another') }}</a>.
            </p>
        </div>
    </div>

@stop
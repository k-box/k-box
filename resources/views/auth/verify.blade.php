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
                {{ trans('mail.verify.if_not_received') }},
            </p>

            <form class="" method="POST" action="{{ route('verification.resend') }}">
                @csrf

                <button type="submit" class="btn btn-link p-0 m-0 align-baseline">
                    {{ trans('mail.verify.request_another') }}
                </button>.
            </form>
        </div>
    </div>

@stop
@extends('layout.full-form')

@push('title')
    {{ trans('mail.verify.verify_email') }} &ndash; 
@endpush

@section('content')

    <div class="">
        <h2 class="mb-1">{{ trans('mail.verify.verify_email') }}</h2>

        <div class=" mb-4">
            @if (session('resent'))
                <div class="c-message c-message--success" role="alert">
                    {{ trans('mail.verify.email_resent') }}
                </div>
            @endif

            <p class="mb-2">
                {{ trans('mail.verify.before_proceeding') }}
            </p>
            <p class="mb-2">
                {{ trans('mail.verify.if_not_received') }}
            </p>

            <form class="" method="POST" action="{{ route('verification.resend') }}">
                @csrf

                <button type="submit" class="btn align-baseline">
                    {{ trans('mail.verify.request_another') }}
                </button>
            </form>
        </div>
    </div>

@stop
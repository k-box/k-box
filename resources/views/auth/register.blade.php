@extends('layout.login')

@push('title')
    @lang('auth.create_account') &ndash; 
@endpush

@section('form')

    <form action="{{ route('register') }}" class="c-form c-form--space" method="POST">

        @csrf
    
        <h2 class="mb-1">{{ trans('auth.create_account') }}</h2>

        @if (Route::has('login'))
            <div class="mb-4">
                {{ trans('auth.have_account') }}&nbsp;<a  tabindex="6" class="" href="{{ route('login') }}">
                    {{ trans('auth.login') }}
                </a>
            </div>
        @endif

        @isset($invite_error)
            <div class="c-message c-message--warning mt-2">
                {{ $invite_error }}
            </div>      
        @endisset

        {{-- <div class=" mb-4">
            <label for="name">{{trans('auth.name_label')}}</label>
            @if( isset($errors) && $errors->has('name') )
                <span class="field-error">{{ $errors->first('name')  }}</span>
            @endif
            <input type="text" class="form-input block w-full sm:w-2/3" required autofocus id="name" name="name" tabindex="1" value="{{ old('name') }}" />
        </div> --}}

        <div class=" mb-4">
            <label for="email" class="">{{trans('auth.email_label')}}</label>

            @if ( isset($errors) && $errors->has('email'))
                <span class="field-error" role="alert">
                    {{ $errors->first('email') }}
                </span>
            @endif
            <input id="email" type="email" class="form-input block w-full sm:w-2/3" name="email" tabindex="2" value="{{ old('email', $email ?? '') }}" required>
        </div>

        <div class=" mb-4">
            <label for="password" class="">{{trans('auth.password_label')}}</label>

            @if ( isset($errors) && $errors->has('password'))
                <span class="field-error" role="alert">
                    {{ $errors->first('password') }}
                </span>
            @endif

            <input id="password" type="password" class="form-input block w-full sm:w-2/3"  tabindex="3" name="password" required>
            <span class="description">{{ trans('profile.labels.password_description') }}</span>
        </div>

        <div class=" mb-4 mb-4">
            <label for="password-confirm" class="">{{ trans('profile.labels.password_confirm') }}</label>

            <input id="password-confirm" type="password" class="form-input block w-full sm:w-2/3" name="password_confirmation"  tabindex="4" required>
        </div>

        @if(isset($invite) || old('invite', false))
            <label for="invite" class="">{{trans('invite.registration-label')}}</label>

            <input type="text" readonly name="invite" value="{{ old('invite', $invite ?? null) }}">
            @if ( isset($errors) && $errors->has('invite'))
                <span class="field-error" role="alert">
                    {{ $errors->first('invite') }}
                </span>
            @endif
        @endif

        <div class=" mb-4">
            <div class="">
                <button type="submit" class="button button--primary"  tabindex="5">
                    {{ trans('auth.register') }}
                </button>
            </div>
        </div>
    </form>

@endsection
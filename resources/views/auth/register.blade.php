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

        {{-- <div class="c-form__field">
            <label for="name">{{trans('auth.name_label')}}</label>
            @if( isset($errors) && $errors->has('name') )
                <span class="field-error">{{ $errors->first('name')  }}</span>
            @endif
            <input type="text" class="c-form__input c-form__input--larger" required autofocus id="name" name="name" tabindex="1" value="{{ old('name') }}" />
        </div> --}}

        <div class="c-form__field">
            <label for="email" class="">{{trans('auth.email_label')}}</label>

            @if ( isset($errors) && $errors->has('email'))
                <span class="field-error" role="alert">
                    {{ $errors->first('email') }}
                </span>
            @endif
            <input id="email" type="email" class="c-form__input c-form__input--larger" name="email" tabindex="2" value="{{ old('email') }}" required>
        </div>

        <div class="c-form__field">
            <label for="password" class="">{{trans('auth.password_label')}}</label>

            @if ( isset($errors) && $errors->has('password'))
                <span class="field-error" role="alert">
                    {{ $errors->first('password') }}
                </span>
            @endif

            <input id="password" type="password" class="c-form__input c-form__input--larger"  tabindex="3" name="password" required>
            <span class="description">{{ trans('profile.labels.password_description') }}</span>
        </div>

        <div class="c-form__field mb-4">
            <label for="password-confirm" class="">{{ trans('profile.labels.password_confirm') }}</label>

            <input id="password-confirm" type="password" class="c-form__input c-form__input--larger" name="password_confirmation"  tabindex="4" required>
        </div>

        <div class="c-form__field">
            <div class="">
                <button type="submit" class="button button--primary"  tabindex="5">
                    {{ trans('auth.register') }}
                </button>
            </div>
        </div>
    </form>

@endsection
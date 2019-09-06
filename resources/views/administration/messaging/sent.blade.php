@extends('global')

@section('breadcrumbs')

    <a href="{{route('administration.index')}}"  class="breadcrumb__item">{{trans('administration.page_title')}}</a> <a href="{{route('administration.users.index')}}"  class="breadcrumb__item">{{trans('administration.menu.accounts')}}</a> <span class="breadcrumb__item--current">{{trans('administration.accounts.create.slug')}}</span>

@stop

@section('content')

    <h3>{{trans('administration.accounts.create.title')}}</h3>

    <form  method="post" action="{{route('administration.users.store')}}">
        
        

          @include('errors.list')


    <input type="hidden" name="_token" value="{{{ csrf_token() }}}"> 

    <p>
        
        <label>{{trans('administration.accounts.labels.email')}}</label>
        @if( $errors->has('email') )
            <span class="field-error">{{ implode(",", $errors->get('email'))  }}</span>
        @endif
        <input type="text" class="form-input" name="email" value="{{old('email', isset($user) ? $user->email : '')}}" @if(isset($can_change_mail) && !$can_change_mail) disabled @endif />
    </p>

    <p>
        
        <label>{{trans('administration.accounts.labels.username')}}</label>
        @if( $errors->has('name') )
            <span class="field-error">{{ implode(",", $errors->get('name'))  }}</span>
        @endif
        <input type="text" class="form-input" name="name" value="{{old('name', isset($user) ? $user->name : '')}}" />
    </p>
        
    </form>
@stop
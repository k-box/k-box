@extends('global')

@section('breadcrumbs')

    <span class="breadcrumb__item--current">{{trans('messaging.create_pagetitle')}}</span>

@stop

@section('content')

    <h3>{{trans('messaging.create_pagetitle')}}</h3>

    <form class="" method="post" action="{{route('administration.messages.store')}}">

        @include('errors.list')

        {{ csrf_field() }}
    
        <div class=" mb-4">
            
            <label>{{trans('messaging.labels.users')}}</label>
            @if( $errors->has('to') )
                <span class="field-error">{{ implode(",", $errors->get('to'))  }}</span>
            @endif
            
            <?php $old = old('to', []); ?>
    		@foreach($available_users as $user)
    			<div class="user-grab">				
    				<input type="checkbox" name="to[]" @if(in_array($user->id, $old)) checked @endif  value="{{$user->id}}" id="user-{{$user->id}}"><label for="user-{{$user->id}}"><span class="btn-icon icon-social-black icon-social-black-ic_person_black_24dp"></span>{{$user->name}}</label>
    			</div>
    		@endforeach

        </div>
    
        <div class=" mb-4">
            
            <label>{{trans('messaging.labels.text')}}</label>
            @if( $errors->has('text') )
                <span class="field-error">{{ implode(",", $errors->get('text'))  }}</span>
            @endif
            <textarea name="text" class="textarea--big">{{old('text', '')}}</textarea>
        </div>
        
        <div class="c-form__buttons">
        
            <button type="submit" class="button button--primary">{{trans('messaging.labels.submit_btn')}}</button> {{trans('actions.or_alt')}} <a href="{{route('administration.users.index')}}">{{trans('actions.cancel')}}</a>
        </div>

        
    </form>
@stop
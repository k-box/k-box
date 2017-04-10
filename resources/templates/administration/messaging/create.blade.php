@extends('management-layout')

@section('sub-header')

    {{trans('messaging.create_pagetitle')}}

@stop

@section('content')

    <h3>{{trans('messaging.create_pagetitle')}}</h3>

    <form  method="post" action="{{route('administration.messages.store')}}">

          @include('errors.list')


        <input type="hidden" name="_token" value="{{{ csrf_token() }}}"> 
    
        <p>
            
            <label>{{trans('messaging.labels.users')}}</label>
            @if( $errors->has('to') )
                <span class="field-error">{{ implode(",", $errors->get('to'))  }}</span>
            @endif
            
            <?php $old = old('to', array()); ?>
    		@foreach($available_users as $user)
    			<div class="user-grab">				
    				<input type="checkbox" name="to[]" @if(in_array($user->id, $old)) checked @endif  value="{{$user->id}}" id="user-{{$user->id}}"><label for="user-{{$user->id}}"><span class="btn-icon icon-social-black icon-social-black-ic_person_black_24dp"></span>{{$user->name}}</label>
    			</div>
    		@endforeach

        </p>
    
        <p>
            
            <label>{{trans('messaging.labels.text')}}</label>
            @if( $errors->has('text') )
                <span class="field-error">{{ implode(",", $errors->get('text'))  }}</span>
            @endif
            <textarea name="text" class="textarea--big">{{old('text', '')}}</textarea>
        </p>
        
        <p>
        
            <button type="submit">{{trans('messaging.labels.submit_btn')}}</button> {{trans('actions.or_alt')}} <a href="{{route('administration.users.index')}}">{{trans('actions.cancel')}}</a>
        </p>

        
    </form>
@stop
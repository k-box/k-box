
    @include('errors.list')

    @if(isset($edit_enabled) && !$edit_enabled)
    
        <div class="c-message c-message--info">
			{!! trans('administration.accounts.edit_disabled_msg', ['profile_url' => route('profile.index')]) !!}
		</div>
    
    @endif


    {{ csrf_field() }}

    <div class=" mb-4">
        <label>{{trans('administration.accounts.labels.email')}}</label>
        @if( $errors->has('email') )
            <span class="field-error">{{ implode(",", $errors->get('email'))  }}</span>
        @endif
        @if( $errors->has('change_email') )
            <span class="field-error">{{ implode(",", $errors->get('change_email'))  }}</span>
        @endif
        <input class="form-input block" type="text" name="email" value="{{old('email', isset($user) ? $user->email : '')}}" @if(isset($can_change_mail) && !$can_change_mail) disabled @endif />
    </div>

    @if(!isset($can_change_mail) || (isset($can_change_mail) && !$can_change_mail))

    <div class=" mb-4">
        @php
            $sending_disabled = (isset($disable_password_sending) && $disable_password_sending);
            $check_send_password = old('send_password', !$sending_disabled || $errors->has('send_password'));
        @endphp

        <label class="block">{{trans('auth.password_label')}}</label>


        @if(isset($disable_password_sending) && $disable_password_sending)
            <span class="description">{{ trans('administration.accounts.labels.no_password_sending') }}</span>
        @else 
            <span class="description">{{ trans('administration.accounts.labels.empty_means_generated_password') }}</span>
        @endif


        @if( $errors->has('password') )
        <span class="field-error">{{ implode(",", $errors->get('password'))  }}</span>
        @endif

        <input class="form-input block mb-2" type="password" name="password" id="password" value="{{old('password', '')}}" />

        <div class="">
            @if( $errors->has('send_password') )
            <span class="field-error">{{ implode(",", $errors->get('send_password'))  }}</span>
            @endif

            <label for="send_password" class="inline-flex items-center {{ isset($disable_password_sending) && $disable_password_sending ? 'opacity-75 cursor-not-allowed' : '' }}">
                <input type="checkbox" class="form-checkbox" value="1" name="send_password" id="send_password" @if(isset($disable_password_sending) && $disable_password_sending) disabled @endif @if($check_send_password) checked @endif>
                <span class="ml-2">{{ trans('administration.accounts.labels.send_password') }}</span>
            </label>
        </div>
    </div>

    @endif

    <div class=" mb-4">
        
        <label>{{trans('administration.accounts.labels.username')}}</label>
        @if( $errors->has('name') )
            <span class="field-error">{{ implode(",", $errors->get('name'))  }}</span>
        @endif
        <input class="form-input block" type="text" name="name" value="{{old('name', isset($user) ? $user->name : '')}}" />
    </div>
    
    @if(!isset($edit_enabled) || (isset($edit_enabled) && $edit_enabled))
    
    <div class=" mb-4">
        <label>{{trans('administration.accounts.labels.perms')}}</label>
        @if( $errors->has('capabilities') )
            <span class="field-error">{{ implode(",", $errors->get('capabilities'))  }}</span>
        @endif
        <div class="my-1">
            @foreach($user_types as $type_key  => $type_value)
                <a href="#" class="user-grab inline-block mr-2" data-type="{{$type_key}}">{{trans('administration.accounts.types.' . $type_key)}}</a>
            @endforeach
        </div>
        <ul class="checkbox-list">
        <?php $olds = old('capabilities', []); ?>
        @foreach($capabilities as $capability)
            @if(isset($type_resolutor[$capability->key]))
            <li class="{{ implode(' ', $type_resolutor[$capability->key])}}"><input type="checkbox" class="{{ implode(' ', $type_resolutor[$capability->key])}}" id="cap-{{$capability->key}}" name="capabilities[]" value="{{$capability->key}}" 
                    @if(isset($user) && in_array($capability->key, $caps) || in_array($capability->key, $olds)) checked @endif >
                    <label for="cap-{{$capability->key}}"> {{trans('administration.accounts.capabilities.' . $capability->key)}}</label></li>
            @endif
        @endforeach
        </ul>
    </div>
    
    @endif
    
    <div class="c-form__buttons">
        <button type="submit" class="button button--primary">{{$submit_text}}</button> {{trans('actions.or_alt')}} <a href="{{route('administration.users.index')}}">{{trans('actions.cancel')}}</a>
    </div>
    
    
    
    <script>
        
        require(["jquery", 'lodash'], function(_$){

          var list = _$('.checkbox-list');
          var timeout = null;

          _$('.user-grab').on('mouseover', function(evt){
             
             var tp = _$(this).data('type');

             if(timeout){
                clearTimeout(timeout);
            }
             
             list.find('.highlighted').removeClass('highlighted');
             list.find('.' + tp +'').addClass('highlighted');
             
            timeout = setTimeout(function (){
                list.find('.highlighted').removeClass('highlighted');

                if(timeout){
                    clearTimeout(timeout);
                }
            }, 2000);

             evt.preventDefault();
             return false; 
          });
          
          _$('.user-grab').on('click', function(evt){
             
             var tp = _$(this).data('type');

             list.find(':checkbox').attr('checked', false);
             list.find('.' + tp +':checkbox').attr('checked', true);
             
             evt.preventDefault();
             return false; 
          });
		
	   });
        
        
    </script>
        
        
        
        
        
